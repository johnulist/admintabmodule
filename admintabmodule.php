<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 08/11/14
 * Time: 16:45
 */

if (!defined('_PS_VERSION_'))
    exit;
defined('PO_CONFIGNAME') or define('PO_CONFIGNAME', 'PO_CONFIGURATION');
defined('PO_CLASSNAME') or define('PO_CLASSNAME', 'PoAdminClass');
defined('PO_TABNAME') or define('PO_TABNAME', 'PavelOskov');
defined('PO_PARENTTABNAME') or define('PO_PARENTTABNAME', 'AdminCatalog');

class admintabmodule extends Module{

    public function __construct()
    {
        $this->name = 'admintabmodule';
        $this->tab = 'quick_bulk_update';
        $this->version = 0.1;
        $this->author = 'Pavel Oskov';

        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Pavel Oskov Prestashop Module');
        $this->description = $this->l('Optimize images by Yahoo Smush.It service.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
        if (!Configuration::get(PO_CONFIGNAME))
            $this->warning = $this->l('No name provided.');
    }

    public function install()
    {
        return
            parent::install() &&
            Configuration::updateValue(PO_CONFIGNAME, 'Configured') &&
            $this->_installModuleTab(PO_CLASSNAME, PO_TABNAME, PO_PARENTTABNAME);
    }


    private function _installModuleTab($tabClass, $tabName, $tabParentName)
    {
        $tab = new Tab();
        //$tab->name = $tabName;
        foreach(Language::getLanguages(false) as $lang){
            $tab->name[(int) $lang['id_lang']] = $tabName;
        }
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = Tab::getIdFromClassName($tabParentName);;
        $tab->active = 1;
        return $tab->save();
    }

    public function uninstall()
    {
        return
            parent::uninstall() &&
            Configuration::deleteByName(PO_CONFIGNAME) &&
            $this->_unInstallTabs(PO_CLASSNAME);
    }

    private function _unInstallTabs($tabClassName)
    {
        if ($id_tab = Tab::getIdFromClassName($tabClassName)) {
            $tab = new Tab((int)$id_tab);
            $tab->delete();
        }
        return true;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name))
        {
            $my_module_name = strval(Tools::getValue(PO_CONFIGNAME));
            if (!$my_module_name
                || empty($my_module_name)
                || !Validate::isGenericName($my_module_name))
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            else
            {
                Configuration::updateValue(PO_CONFIGNAME, $my_module_name);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Configuration value'),
                    'name' => PO_CONFIGNAME,
                    'size' => 20,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                        '&token='.Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value[PO_CONFIGNAME] = Configuration::get(PO_CONFIGNAME);

        return $helper->generateForm($fields_form);
    }

} 