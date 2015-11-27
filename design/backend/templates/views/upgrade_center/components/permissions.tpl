{if $data == "ok"}
    <table class="table table-condensed">
        <thead>
            <tr>
                <!-- upgrade-center_warning or upgrade-center_error -->
                <th class="upgrade-center_table-title">
                    <h4>{__("permissions_issue")}</h4>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="upgrade-center_info">{__("ok")}</td></tr>
        </tbody>
    </table>
{else}
    <table class="table table-condensed">
        <thead>
            <tr>
                <!-- upgrade-center_warning or upgrade-center_error -->
                <th class="upgrade-center_table-title upgrade-center_error">
                    <h4>{__("permissions_issue")}</h4>
                    <p>{__("text_uc_non_writable_files")}</p>
                </th>
                <th class="upgrade-center_actions">
                    <a href="{"upgrade_center.ftp_settings?package_id=`$id`&package_type=`$type`"|fn_url}" class="btn cm-dialog-keep-in-place cm-dialog-opener" data-ca-target-id="auto_set_permissions_{$id}">{__("auto_set_permissions_via_ftp")}</a>
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach $data as $filename}
            <tr>
                <td colspan="2">{$filename}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>

    <div id="auto_set_permissions_{$id}" title="{__("ftp_server_options")}">
    </div>
{/if}