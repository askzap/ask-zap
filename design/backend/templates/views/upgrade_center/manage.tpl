{script src="js/tygh/tabs.js"}

{capture name="mainbox"}
    <div class="upgrade-center">
        {foreach $upgrade_packages as $type => $packages}
            {foreach $packages as $_id => $package}
                {$id = $_id|replace:".":"_"}

                <form name="upgrade_form_{$type}_{$id}" method="post" action="{fn_url()}" class="form-horizontal form-edit cm-disable-check-changes">
                    <input type="hidden" name="type" value="{$type}">
                    <input type="hidden" name="id" value="{$_id}">
                    <input type="hidden" name="result_ids" value="install_notices_{$id},install_button_{$id}">

                    <div class="upgrade-center_item">
                        <div class="upgrade-center_icon">
                            {if $type == "core" || $type == "hotfix"}
                                <i class="glyph-physics1"></i>
                            {else}
                                <i class="glyph-addon"></i>
                            {/if}
                        </div>

                        <div class="upgrade-center_content">
                            <h4 class="upgrade-center_title">{$package.name}</h4>
                            <ul class="upgrade-center_info">
                                <li> <strong>{__("new_version")}:</strong> {$package.to_version}</li>
                                <li> <strong>{__("release_date")}:</strong> {$package.timestamp|date_format}</li>
                                <li> <strong>{__("filesize")}:</strong> {$package.size|formatfilesize nofilter}</li>
                            </ul>
                            <p class="upgrade-center_desc">
                                {$package.description|replace:"\n":"<br>" nofilter}
                            </p>

                            {if $package.ready_to_install}
                                {include file="views/upgrade_center/components/install_button.tpl" id=$id caption=__("install")}

                                <a class="upgrade-center_pkg cm-dialog-opener cm-ajax" href="{"upgrade_center.package_content?package_id=`$_id`"|fn_url}" data-ca-target-id="package_content_{$id}" data-ca-dialog-title="{$package.name|escape}">{__("package_contents")}</a>

                            {else}
                                <div class="upgrade-center_install">
                                    <input name="dispatch[upgrade_center.download]" type="submit" class="btn cm-loading-btn" value="{__("download")}" data-ca-loading-text="{__("loading")}">
                                </div>
                            {/if}
                                
                            {include file="views/upgrade_center/components/notices.tpl" id=$id type=$type}
                        </div>
                    </div>
                </form>
            {/foreach}
        {foreachelse}
            <p class="no-items">{__('text_no_upgrades_available')}</p>
        {/foreach}
    </div>

    <script type="text/javascript">

        (function(_, $){
            $('.cm-loading-btn').on('click', function() {
                var self = $(this);
                setTimeout(function() {
                    self.prop('value', self.data('caLoadingText'));
                    $('.cm-loading-btn').attr('disabled', true);
                }, 50);
                return true;
            });
        })(Tygh, Tygh.$);
    </script>
    
{/capture}

{capture name="buttons"}
    {capture name="tools_list"}
        <li>{btn type="list" text=__("refresh_packages_list") href="upgrade_center.refresh"}</li>
        <li>{btn type="list" text=__("settings") href="settings.manage&section_id=Upgrade_center"}</li>
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
    {$smarty.capture.install_btn nofilter}
    {if $installed_upgrades.has_upgrades}
        {include file="buttons/button.tpl" but_href="upgrade_center.installed_upgrades" but_text=__("installed_upgrades") but_role="link"}
    {/if}
{/capture}

{capture name="upload_upgrade_package"}
    {include file="views/upgrade_center/components/upload_upgrade_package.tpl"}
{/capture}

{capture name="adv_buttons"}
    {hook name="upgrade_center:adv_buttons"}
        {include file="common/popupbox.tpl" id="upload_upgrade_package_container" text=__("upload_upgrade_package") title=__("upload_upgrade_package") content=$smarty.capture.upload_upgrade_package act="general" link_class="cm-dialog-auto-size" icon="icon-plus" link_text=""}
    {/hook}
{/capture}

{include file="common/mainbox.tpl" title=__("upgrade_center") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons sidebar=$smarty.capture.sidebar}
