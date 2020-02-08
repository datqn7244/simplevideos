<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class SimpleVIDEOS extends Module
{
    public function __construct()
    {
        $this->name = 'simplevideos';
        $this->author = 'Dat Nguyen';
        $this->version = '1.0.0';
        $this->bootstrap = true;
        parent:: __construct();
        $this->displayName = $this->l('Simple video module');
        $this->description = $this->l('Module to display Youtube video on product page');
        $this->confirmUninstall = $this->l('Are you sure?');
    }

    public function install()
    {
        // A table to store video information will be create within the installation process
        include_once($this->local_path.'sql/install.php');
        if (!parent::install()) {
            return false;
        }
        if (!$this->registerHook('displayProductExtraContent')) {
            return false;
        }
        if (!$this->registerHook('displayAdminProductsExtra')) {
            return false;
        }
        if (!$this->registerHook('actionProductSave')) {
            return false;
        }
        // After register all the hook, a value will be add to the database
        // to enable the usage of product video.
        Configuration::updateValue('SIMPLEVIDEOS_VIDEOS', '1');
        return true;
    }

    public function uninstall()
    {
        // Installed database will be deleted when uninstall
        include_once($this->local_path.'sql/uninstall.php');
        if (!parent::uninstall()) {
            return false;
        }
        // the added value will also be deleted.
        Configuration::deleteByName('SIMPLEVIDEOS_VIDEOS');
        return  true;
    }

    //Display Module Configuration
    public function getContent()
    {
        $this->processConfiguration();
        $html_form = $this->renderForm();
        return $html_form;
    }
    //Receive the value from the configuration form and update the database
    public function processConfiguration()
    {
        if (Tools::isSubmit('simple_videos_form')) {
            $enable_videos = Tools::getValue('enable_videos');
            Configuration::updateValue('SIMPLEVIDEOS_VIDEOS', $enable_videos);
        }
    }
    // Display the Product video tab on product page with DisplayProductExtraContent hook
    public function hookDisplayProductExtraContent($params)
    {
        $enable_videos = Configuration::get('SIMPLEVIDEOS_VIDEOS');
        $id_product = Tools::getValue('id_product');
        $sqls='SELECT `id_video`, `enable` FROM `'._DB_PREFIX_.'simplevideos` WHERE `id_product` ='.(int)$id_product;
        $video = Db::getInstance()->executeS($sqls);
        $array = array();
        // Only assign value to smarty if the product video setting is set
        if(isset($video[0])){
            $this->context->smarty->assign(array(
            'enable_videos' => $enable_videos,
            'video' =>$video[0]
        ));
        $templateFile = 'module:simplevideos/views/templates/hook/simplevideos.tpl';
        // Only display the product video tab if it's enabled in the module configuration and product back office, and the id_video is not empty.
        if ($enable_videos==1 && $video[0]['enable']==1 && $video[0]['id_video']!="") {
            $array[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent()) ->setTitle('Product Video') ->setContent($this->fetch($templateFile));
        }
        return $array;
        }

    }
    // Add option to product back office under Modules tab
    public function hookDisplayAdminProductsExtra($params)
    {
        $enable_videos = Configuration::get('SIMPLEVIDEOS_VIDEOS');
        $id_product = (int)$params['id_product'];
        $sqls='SELECT * FROM `'._DB_PREFIX_.'simplevideos` WHERE `id_product` ='.$id_product.'';
        $video= Db::getInstance()->executeS($sqls);
        $this->context->smarty->assign(array(
            'enable_videos' => $enable_videos,
            'video'=> $video[0]
        ));
        return $this->display(__FILE__, 'views/templates/admin/productconfigs.tpl');
    }
    // use ActionProductSave hook to retrieve value from POST
    // and update value to the database
    public function hookActionProductSave()
    {
        $enable_videos = Tools::getValue('enable_videos');
        // Only change value if user decides to enable 
        if ($enable_videos==1) {
            Configuration::updateValue('SIMPLEVIDEOS_VIDEOS', $enable_videos);
        }
        // Using hidden input as an indicator for updating specific product video
        $update_video = (int)Tools::getValue('update_video');
        if($update_video==1){
        $id_product = (int)Tools::getValue('id_product');
        $id_video =  pSQL(Tools::getValue('id_video'));
        $enable = (int)Tools::getValue('enable_video');
        $date_add = date('Y-m-d H:i:s');
        // The query is to insert or update if the query is already exist. 
        $sqls='INSERT INTO `'._DB_PREFIX_.'simplevideos` (`id_product`, `id_video`, `enable`, `date_add`) 
            VALUES("'.$id_product.'", "'.$id_video.'",'.$enable.',"'.$date_add.'") ON DUPLICATE KEY 
            UPDATE `id_video`="'.$id_video.'", `enable`='.$enable.', `date_add`="'.$date_add.'" ';
        Db::getInstance()->execute($sqls);}
    }
    // RenderForm for module configuration page
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
            'legend' => array(
            'title' => $this->l('My Module configuration'),
            'icon' => 'icon-envelope'
            ),
            'input' => array(
        array(
        'type' => 'switch',
        'label' => $this->l('Enable Videos:'),
        'name' => 'enable_videos',
        'desc' => $this->l('Enable videos on products.'),
        'values' => array(
        array(
        'id' => 'enable_videos_1',
        'value' => 1,
        'label' => $this->l('Enabled')
        ),
        array(
        'id' => 'enable_videos_0',
        'value' => 0,
        'label' => $this->l('Disabled')
        )
        ),
        ),
        ),
        'submit' => array(
        'title' => $this->l('Save'),
        )
        ),
       );
        $helper = new HelperForm();
        $helper->table = 'simplevideos';
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->submit_action = 'simple_videos_form';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array('fields_value' => array(
        'enable_videos' => Tools::getValue(
            'enable_videos',
            Configuration::get('SIMPLEVIDEOS_VIDEOS')
        ),
        ),
        'languages' => $this->context->controller->getLanguages()
    );
        return $helper->generateForm(array($fields_form));
    }
}
