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

namespace Tygh;

use Tygh\Registry;
use Tygh\Validators;

class DataKeeper
{
    const ERROR_UNSUPPORTED_FILE_TYPE = 'datakeeper.error_unsupported_file_type';
    const ERROR_UNWRITABLE_FILE = 'datakeeper.file_cannot_be_overrided';

    /**
     * Makes a full backup of store
     *
     * @return bool true if successfully created
     */
    public static function backup($params = array())
    {
        $tables = db_get_fields('SHOW TABLES');
        $default_params = array(
            'compress' => 'zip',
            'db_tables' => $tables,
            'db_schema' => true,
            'db_data' => true,
            'move_progress' => true,
        );

        $pack_name = !empty($params['pack_name']) ? $params['pack_name'] : date('dMY_His', TIME);
        $destination_path = fn_get_cache_path(false) . 'tmp/backup/';

        $params = array_merge($default_params, $params);

        $files = self::backupFiles($params);
        $dump = self::backupDatabase($params);

        fn_rm($destination_path . $pack_name);
        fn_mkdir($destination_path . $pack_name);

        fn_copy($files, $destination_path . $pack_name);
        fn_mkdir($destination_path . $pack_name . '/var/restore/');
        fn_copy($dump, $destination_path . $pack_name . '/var/restore/');

        fn_rm($files);
        fn_rm($dump);

        if (!empty($params['compress'])) {
            fn_set_progress('echo', __('compressing_backup'), false);

            $ext = $params['compress'] == 'tgz' ? '.tgz' : '.zip';
            $result = fn_compress_files($pack_name . $ext, $pack_name, $destination_path);

            fn_rm($destination_path . $pack_name);

            if ($result) {
                // Move archive to backups directory
                $result = fn_rename(
                    $destination_path . $pack_name . $ext,
                    Registry::get('config.dir.backups') . $pack_name . $ext
                );

                if ($result) {
                    return Registry::get('config.dir.backups') . $pack_name . $ext;
                }
            }

            return false;
        } else {
            return $destination_path . $pack_name;
        }
    }

    /**
     * Makes store files backup
     *
     * @param array $params Extra params
     *  backup_files - array List of files/folders to be added to backup
     *  pack_name - string name of result pack. Will be stored in Registry::get('config.dir.backups') . 'files/' . $pack_name
     *  fs_compress - bool Compress result dir
     * @return string Path to backuped files/archve
     */
    public static function backupFiles($params = array())
    {
        $backup_files = array(
            'app',
            'design',
            'js',
            '.htaccess',
            'api.php',
            'config.local.php',
            'config.php',
            'index.php',
            'init.php',
            'robots.txt',
            'var/themes_repository',
            'var/snapshots'
        );

        $backup_files[] = Registry::get('config.admin_index');

        if (fn_allowed_for('MULTIVENDOR')) {
            $backup_files[] = Registry::get('config.vendor_index');
        }

        if (!empty($params['backup_files'])) {
            $backup_files = $params['backup_files'];
        }

        if (!empty($params['extra_folders'])) {
            $params['extra_folders'] = array_map(function($path){
                return fn_normalize_path($path);
            }, $params['extra_folders']);

            $backup_files = array_merge($backup_files, $params['extra_folders']);
        }

        fn_set_hook('data_keeper_backup_files', $backup_files);

        $pack_name = !empty($params['pack_name']) ? $params['pack_name'] : 'backup_' . date('dMY_His', TIME);
        $destination_path = fn_get_cache_path(false) . 'tmp/backup/_files/' . $pack_name;
        $source_path = Registry::get('config.dir.root' . '/');

        fn_set_progress('step_scale', (sizeof($backup_files) + 1) * 2);
        fn_set_progress('echo', __('backup_files'), false);

        fn_rm($destination_path);
        fn_mkdir($destination_path);

        foreach ($backup_files as $file) {
            fn_set_progress('echo', __('uc_copy_files') . ': <b>' . $file . '</b>', true);
            fn_copy($source_path . $file, $destination_path . '/' . $file);
        }

        if (!empty($params['fs_compress'])) {
            fn_set_progress('echo', __('compressing_backup'), true);

            $ext = $params['fs_compress'] == 'tgz' ? '.tgz' : '.zip';
            $result = fn_compress_files($pack_name . $ext, $pack_name, fn_get_cache_path(false) . 'tmp/backup/_files/');
            $destination_path = rtrim($destination_path, '/');

            if ($result) {
                fn_rename($destination_path . $ext, Registry::get('config.dir.backups') . $pack_name . $ext);
            }
            fn_rm($destination_path);

            $destination_path .= $ext;
        }

        return $destination_path;
    }

    /**
     * Makes DB backup
     *  db_filename - string name of result pack. Will be stored in Registry::get('config.dir.database') . $db_filename;
     *  db_tables - array List of tables to be backuped
     *  db_schema - bool Backup tables schema
     *  db_data - bool Backup data from tables
     * @param array $params
     *
     * @return string Path to backuped DB sql/tgz file
     */
    public static function backupDatabase($params = array())
    {
        $default_params = array(
            'db_tables' => array(),
            'db_schema' => false,
            'db_data' => false,
            'db_compress' => false,
            'move_progress' => true,
        );

        $params = array_merge($default_params, $params);

        $db_filename = empty($params['db_filename']) ? 'dump_' . date('mdY') . '.sql' : fn_basename($params['db_filename']);

        if (!fn_mkdir(Registry::get('config.dir.backups'))) {
            fn_set_notification('E', __('error'), __('text_cannot_create_directory', array(
                '[directory]' => fn_get_rel_dir(Registry::get('config.dir.backups'))
            )));

            return false;
        }

        $dump_file = Registry::get('config.dir.backups') . $db_filename;

        if (is_file($dump_file)) {
            if (!is_writable($dump_file)) {
                fn_set_notification('E', __('error'), __('dump_file_not_writable'));

                return false;
            }
        }

        $result = db_export_to_file($dump_file, $params['db_tables'], $params['db_schema'], $params['db_data'], true, true, $params['move_progress']);

        if (!empty($params['db_compress'])) {
            fn_set_progress('echo', __('compress_dump'), false);

            $ext = $params['db_compress'] == 'tgz' ? '.tgz' : '.zip';
            $result = fn_compress_files($db_filename . $ext, $db_filename, dirname($dump_file));
            unlink($dump_file);

            $dump_file .= $ext;
        }

        if ($result) {
            return $dump_file;
        }

        return false;
    }

    /**
     * Restores backup file
     *
     * @param  string $filename  File to be restored
     * @param  string $base_path Base folder path (default: dir.backups)
     * @return bool   true if restored, error code if errors
     */
    public static function restore($filename, $base_path = '')
    {
        $file_ext = fn_get_file_ext($filename);

        if (!in_array($file_ext, array('sql', 'tgz', 'zip'))) {
            return __(self::ERROR_UNSUPPORTED_FILE_TYPE);
        }

        if (empty($base_path)) {
            $base_path = Registry::get('config.dir.backups');
        }

        $backup_path = $base_path . basename($filename);

        if (in_array($file_ext, array('zip', 'tgz'))) {
            $type = self::getArchiveType($backup_path);

            $extract_path = fn_get_cache_path(false) . 'tmp/backup/';
            fn_rm($extract_path);
            fn_mkdir($extract_path);

            if ($type == 'database') {
                fn_decompress_files($backup_path, $extract_path);
                $list = fn_get_dir_contents($extract_path, false, true, 'sql');

                foreach ($list as $sql_file) {
                    db_import_sql_file($extract_path . $sql_file);
                }
            } else {
                $root_dir = Registry::get('config.dir.root') . '/';
                $files_list = self::getCompressedFilesList($backup_path);

                // Check permissions on all files
                foreach ($files_list as $file) {
                    if (!self::checkWritable($root_dir . $file)) {
                        return __(self::ERROR_UNWRITABLE_FILE, array('[file]' => $root_dir . $file, '[url]' => fn_url('settings.manage?section_id=Upgrade_center')));
                    }

                    fn_set_progress('echo', __('check_permissions') . ': ' . $file . '<br>', true);
                }

                // All files can be overrided. Restore backupped files
                fn_decompress_files($backup_path, $extract_path);
                $root_dir = Registry::get('config.dir.root') . '/';

                foreach ($files_list as $file) {
                    $ext = fn_get_file_ext($file);
                    if ($ext == 'sql' && strpos($file, 'var/restore/') !== false) {
                        // This is a DB dump. Restore it
                        db_import_sql_file($extract_path . $file);
                        continue;
                    }

                    fn_set_progress('echo', __('restore') . ': ' . $file . '<br>', true);

                    self::restoreFile($extract_path . $file, $root_dir . $file);
                }

                fn_rm($extract_path);

                return true;
            }

        } else {
            db_import_sql_file($backup_path);
        }

        fn_log_event('database', 'restore');
        fn_clear_cache();

        return true;
    }

    /**
     * Gets DataKeeper archive type
     *
     * @param  string $path Path to file
     * @return string Type (database/files/full)
     */
    public static function getArchiveType($path)
    {
        $archive_type = 'unknown';
        $files_list = self::getCompressedFilesList($path, true);

        // Detect archive type: Database, Files, Full
        if (!empty($files_list)) {
            $type = array(
                'database' => false,
                'files' => false,
            );

            if (count($files_list) == 1 && fn_get_file_ext($files_list[0]) == 'sql') {
                $type['database'] = true;
            } else {
                $type['files'] = true;

                $files_list = self::getCompressedFilesList($path, false);
                foreach ($files_list as $filename) {
                    if (strpos($filename, 'var/restore/') !== false) {
                        $type['database'] = true;
                        break;
                    }
                }
            }

            if ($type['database'] && $type['files']) {
                $archive_type = 'full';
            } elseif ($type['database']) {
                $archive_type = 'database';
            } elseif ($type['files']) {
                $archive_type = 'files';
            }
        }

        return $archive_type;
    }

    /**
     * Gets archive file tree without unpacking
     *
     * @param  string $file_path Path to packed file
     * @param  bool   $only_root gets only root folders and files
     * @return mixed  List of files in packed archive or false if archive cannot be read or archive does not support
     */
    public static function getCompressedFilesList($file_path, $only_root = false)
    {
        $files_list = array();
        $ext = fn_get_file_ext($file_path);

        switch ($ext) {
            case 'zip':
                $validators = new Validators();
                if (!$validators->isZipArchiveAvailable()) {
                    return $files_list;
                }

                $zip = new \ZipArchive;

                if ($zip->open($file_path)) {
                    $num_files = $zip->numFiles;

                    for ($i = 0; $i < $num_files; $i++) {
                        $file_name = $zip->getNameIndex($i);
                        if ($only_root && strpos($file_name, '/') !== false) {
                            $file_name = preg_replace('#/.*$#i', '', $file_name);
                        }
                        $files_list[$file_name] = $i;
                    }

                    $files_list = array_flip($files_list);
                }

                break;

            case 'tgz':
                if ($only_root) {
                    $tgz = new \PharData($file_path);
                    if (!empty($tgz)) {
                        foreach ($tgz as $index => $file) {
                            $files_list[] = $file->getFilename();
                        }
                    }
                } else {
                    $files_list = fn_get_dir_contents('phar://' . $file_path, true, true, '', '', true);

                    return $files_list;
                }

                break;

            default:
                break;
        }

        return $files_list;
    }

    /**
     * Checks if file has writable permissions
     * @param  string $path          Path to file
     * @param  bool   $restore_perms Save the same permissions after checking
     * @return bool   true if has, false otherwise
     */
    protected static function checkWritable($path, $restore_perms = true)
    {
        if (file_exists($path) || is_dir($path)) {
            if (!is_writable($path)) {
                $old_perms = substr(sprintf('%o', fileperms($path)), -4);
                @chmod($path, 0777);
                if (!is_writable($path)) {
                    return self::checkFtpWritable($path, $restore_perms);
                } else {
                    if ($restore_perms) {
                        @chmod($path, intval($old_perms, 8));
                    }

                    return true;
                }
            }
        } else {
            return self::checkWritable(dirname($path), $restore_perms);
        }

        return true;
    }

    /**
     * Checks if file has writable permissions (FTP)
     *
     * @param  string $path          Path to file
     * @param  bool   $restore_perms Save the same permissions after checking
     * @return bool   true if has, false otherwise
     */
    protected static function checkFtpWritable($path, $restore_perms = true)
    {
        static $ftp_link = null;
        static $ftp_connection_status = false;

        if (empty($ftp_link) && !$ftp_connection_status) {
            if (fn_ftp_connect(Registry::get('settings.Upgrade_center'), true)) {
                $ftp_link = Registry::get('ftp_connection');
            }

            $ftp_connection_status = true;
        }

        if (empty($ftp_link)) {
            return false;
        }

        $old_perms = substr(sprintf('%o', fileperms($path)), -4);
        fn_ftp_chmod_file($path, 0777);

        $result = is_writable($path);

        if ($restore_perms) {
            fn_ftp_chmod_file($path, intval($old_perms, 8));
        }

        return $result;
    }

    /**
     * Restores file from the backup archive
     *
     * @param string $source      Path to source file
     * @param string $destination Path to destination file
     */
    protected static function restoreFile($source, $destination)
    {
        if (file_exists($destination) || is_dir($destination)) {
            $old_perms = substr(sprintf('%o', fileperms($destination)), -4);
        }

        if (self::checkWritable($destination, false)) {
            if (is_dir($source)) {
                fn_mkdir($destination);
            } else {
                fn_copy($source, $destination);
            }
        }
    }
}
