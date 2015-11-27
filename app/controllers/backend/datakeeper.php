<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Registry;
use Tygh\DataKeeper;
use Tygh\Validators;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

    set_time_limit(0);

    // Backup database
    if ($mode == 'backup') {

        if (!empty($_REQUEST['backup_database']) && $_REQUEST['backup_database'] == 'Y' && !empty($_REQUEST['backup_files']) && $_REQUEST['backup_files'] == 'Y') {
            $mode = 'both';
        } elseif (!empty($_REQUEST['backup_database']) && $_REQUEST['backup_database'] == 'Y') {
            $mode = 'database';
        } elseif (!empty($_REQUEST['backup_files']) && $_REQUEST['backup_files'] == 'Y') {
            $mode = 'files';
        }

        switch ($mode) {
            case 'both':
                $params = array(
                    'compress' => !empty($_REQUEST['compress_type']) ? $_REQUEST['compress_type'] : 'zip',
                    'db_filename' => empty($_REQUEST['dbdump_filename']) ? date('dMY_His', TIME) . '.sql' : fn_basename($_REQUEST['dbdump_filename']) . '.sql',
                    'db_tables' => empty($_REQUEST['dbdump_tables']) ? array() : $_REQUEST['dbdump_tables'],
                    'db_schema' => !empty($_REQUEST['dbdump_schema']) && $_REQUEST['dbdump_schema'] == 'Y',
                    'db_data' => !empty($_REQUEST['dbdump_data']) && $_REQUEST['dbdump_data'] == 'Y',
                    'pack_name' => empty($_REQUEST['dbdump_filename']) ? date('dMY_His', TIME) : fn_basename($_REQUEST['dbdump_filename']),
                    'extra_folders' => !empty($_REQUEST['extra_folders']) ? $_REQUEST['extra_folders'] : array(),
                );

                DataKeeper::backup($params);

                break;

            case 'database':
                $params = array(
                    'db_filename' => empty($_REQUEST['dbdump_filename']) ? date('dMY_His', TIME) . '.sql' : fn_basename($_REQUEST['dbdump_filename']) . '.sql',
                    'db_tables' => empty($_REQUEST['dbdump_tables']) ? array() : $_REQUEST['dbdump_tables'],
                    'db_schema' => !empty($_REQUEST['dbdump_schema']) && $_REQUEST['dbdump_schema'] == 'Y',
                    'db_data' => !empty($_REQUEST['dbdump_data']) && $_REQUEST['dbdump_data'] == 'Y',
                    'db_compress' => !empty($_REQUEST['compress_type']) ? $_REQUEST['compress_type'] : 'zip',
                );

                $dump_file_path = DataKeeper::backupDatabase($params);

                if (!empty($dump_file_path)) {
                    fn_set_notification('N', __('notice'), __('done'));
                }
                break;

            case 'files':
                $params = array(
                    'pack_name' => empty($_REQUEST['dbdump_filename']) ? date('dMY_His', TIME) : fn_basename($_REQUEST['dbdump_filename']),
                    'fs_compress' => !empty($_REQUEST['compress_type']) ? $_REQUEST['compress_type'] : 'zip',
                    'extra_folders' => !empty($_REQUEST['extra_folders']) ? $_REQUEST['extra_folders'] : array(),
                );

                $dump_file_path = DataKeeper::backupFiles($params);

                if (!empty($dump_file_path)) {
                    fn_set_notification('N', __('notice'), __('done'));
                }
                break;
        }
    }

    // Restore
    if ($mode == 'restore') {
        if (!empty($_REQUEST['backup_file'])) {
            $restore_result = DataKeeper::restore($_REQUEST['backup_file']);
            if ($restore_result === true) {
                fn_set_notification('N', __('notice'), __('done'));
            } else {
                fn_set_notification('E', __('error'), $restore_result);
            }
        }
    }

    if ($mode == 'm_delete') {
        if (!empty($_REQUEST['backup_files'])) {
            foreach ($_REQUEST['backup_files'] as $file) {
                @unlink(Registry::get('config.dir.backups') . fn_basename($file));
            }
        }
    }

    if ($mode == 'upload') {
        $dump = fn_filter_uploaded_data('dump', array('sql', 'tgz', 'zip'));

        if (!empty($dump)) {
            $dump = array_shift($dump);
            // Check if backups folder exists. If not - create it
            if (!is_dir(Registry::get('config.dir.backups'))) {
                fn_mkdir(Registry::get('config.dir.backups'));
            }

            if (fn_copy($dump['path'], Registry::get('config.dir.backups') . $dump['name'])) {
                fn_set_notification('N', __('notice'), __('done'));
            } else {
                fn_set_notification('E', __('error'), __('cant_create_backup_file'));
            }
        } else {
            fn_set_notification('E', __('error'), __('cant_upload_file'));
        }
    }

    if ($mode == 'optimize') {
        // Log database optimization
        fn_log_event('database', 'optimize');

        $all_tables = db_get_fields("SHOW TABLES");

        fn_set_progress('parts', sizeof($all_tables));

        foreach ($all_tables as $table) {
            fn_set_progress('echo', __('optimizing_table') . "&nbsp;<b>$table</b>...<br />");

            db_query("OPTIMIZE TABLE $table");
            db_query("ANALYZE TABLE $table");
            $fields = db_get_hash_array("SHOW COLUMNS FROM $table", 'Field');

            if (!empty($fields['is_global'])) { // Sort table by is_global field
                fn_echo('.');
                db_query("ALTER TABLE $table ORDER BY is_global DESC");
            } elseif (!empty($fields['position'])) { // Sort table by position field
                fn_echo('.');
                db_query("ALTER TABLE $table ORDER BY position");
            }
        }

        fn_set_notification('N', __('notice'), __('done'));
    }

    if ($mode == 'delete') {
        if (!empty($_REQUEST['backup_file'])) {
            fn_rm(Registry::get('config.dir.backups') . fn_basename($_REQUEST['backup_file']));
        }
    }

    return array(CONTROLLER_STATUS_OK, 'datakeeper.manage');
}

if ($mode == 'getfile' && !empty($_REQUEST['file'])) {
    fn_get_file(Registry::get('config.dir.backups') . fn_basename($_REQUEST['file']));

} elseif ($mode == 'manage') {
    $view = Tygh::$app['view'];

    // Calculate database size and fill tables array
    $status_data = db_get_array("SHOW TABLE STATUS");
    $database_size = 0;
    $all_tables = array();
    foreach ($status_data as $k => $v) {
        $database_size += $v['Data_length'] + $v['Index_length'];
        $all_tables[] = $v['Name'];
    }

    $view->assign('database_size', $database_size);
    $view->assign('all_tables', $all_tables);

    $files = fn_get_dir_contents(Registry::get('config.dir.backups'), false, true, array('.sql', '.tgz', '.zip'), '', true);

    sort($files, SORT_STRING);
    $backup_files = array();
    $date_format = Registry::get('settings.Appearance.date_format'). ' ' . Registry::get('settings.Appearance.time_format');
    if (is_array($files)) {
        $backup_dir = Registry::get('config.dir.backups');
        foreach ($files as $file) {
            $ext = fn_get_file_ext($backup_dir . $file);

            $backup_files[$file]['size'] = filesize($backup_dir . $file);
            $backup_files[$file]['create'] = fn_date_format(filemtime($backup_dir . $file), $date_format);

            if ($ext == 'sql') {
                $backup_files[$file]['type'] = 'database';
            } else {
                $backup_files[$file]['type'] = DataKeeper::getArchiveType($backup_dir . $file);
            }
        }
    }

    $supported_archive_types = array(
        'tgz'
    );
    $validators = new Validators();
    if ($validators->isZipArchiveAvailable()) {
        $supported_archive_types[] = 'zip';
    }

    $view->assign('supported_archive_types', $supported_archive_types);
    $view->assign('backup_files', $backup_files);
    $view->assign('backup_dir', fn_get_rel_dir(Registry::get('config.dir.backups')));
}
