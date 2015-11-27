<table class="table table-condensed">
    <thead>
        <tr>
            <!-- upgrade-center_warning or upgrade-center_error -->
            <th class="upgrade-center_table-title upgrade-center_error">
                <h4>{__("upgrade_center.validation_issue")}</h4>
                <p>{__("upgrade_center.validator_fail_result", ["[validator_name]" => $validator_name])}</p>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                {$data nofilter}
            </td>
        </tr>
    </tbody>
</table>