<table class="table table-condensed">
    <thead>
        <tr>
            <!-- upgrade-center_warning or upgrade-center_error -->
            <th class="upgrade-center_table-title upgrade-center_error">
                <h4>{__("text_uc_local_modification")}</h4>
                <p>{__("text_uc_changed_files")}</p>
            </th>
        </tr>
    </thead>
</table>

<div class="upgrade-center_collisions">
    <table class="table table-condensed">
        <thead>
            <tr>
                <th>{__("files")}</th>
                <th class="right">{__("action")}</th>
            </tr>
        </thead>
        <tbody>
        {foreach $data as $status => $files}
            {foreach $files as $file_path}
                <tr>
                    <td>
                        {$file_path}
                    </td>
                    <td width="10%" class="right">
                        {if $status == "changed" || $status == "new"}
                            <span class="label label-warning">{__("text_uc_will_be_changed")}</span>
                        {elseif $status == "deleted"}
                            <span class="label label-important">{__("text_uc_will_be_deleted")}</span>
                        {/if}
                        
                    </td>
                </tr>
            {/foreach}
        {/foreach}
        </tbody>
    </table>
</div>

<strong>{__("text_uc_agreed_collisions")}</strong>

<div class="control-group">
    <label class="control-label cm-required" for="skip_collisions_{$id}">{__("confirm")}</label>
    <div class="controls">
        <input type="checkbox" id="skip_collisions_{$id}" name="skip_collisions" value="Y">
    </div>
</div>