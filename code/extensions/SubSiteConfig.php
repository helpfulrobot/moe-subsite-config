<?php

/**
 * Class SubSiteConfig
 *
 * Requires users to have developer access to modify. Removes the chance of CMS administrators changing values.
 */
class SubSiteConfig extends DataExtension implements PermissionProvider
{

    private static $db = array(
        'SubSiteConstant' => 'Varchar(255)'
    );

    /**
     *
     * Check that you have entered values in a config file that match the constant set in the subsite Settings by the developer
     *
     * @param string $key Name of the array in the _config.yml file
     * @param string $value Array value set under the name
     * @return bool
     */
    public static function display($key, $value)
    {
        $subSiteConstant = SiteConfig::current_site_config()->SubSiteConstant;
        $config = Config::inst()->get($key, $value);

        return Subsite::currentSubsite() && in_array($subSiteConstant, $config) ? true : false;
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab("Root.AdditionalSettings", array(
            TextField::create('SubSiteConstant', 'Subsite Constant')
                ->setAttribute('placeholder', 'SUBSITE_NAME')
                ->setDescription('Used as a guarantee for module loading. <a href="https://gitlab.cwp.govt.nz/modules/subsite-config/wikis/SubSiteConfig" target="_blank">Details here</a>'),
        ));

        // Make sure only a developer can change this value
        if (!Permission::check('SUBSITE_DEVELOPER_EDIT')) {
            $fields->makeFieldReadonly('SubSiteConstant');
        }

        return $fields;
    }

    /**
     *
     * Changing the constant set by the developer can cause havoc on a site. Its important this is set once and not modified by a site admin/content editor without
     * explicit instructions from the sites developer
     *
     * @return array
     */
    public function providePermissions()
    {
        return array(
            'SUBSITE_DEVELOPER_EDIT' => array(
                'name'     => 'Edit developer settings',
                'category' => 'Developer Specific Settings'
            ),
        );
    }

    public function canEdit($member = null)
    {
        return Permission::check('SUBSITE_DEVELOPER_EDIT');
    }

}
