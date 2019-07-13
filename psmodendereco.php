<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Psmodendereco extends Module
{
    protected $config_form = false;

    public $modPostcode;
    public $modDataAddress;

    public function __construct()
    {
        $this->name = 'psmodendereco';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Ederson Ferreira da Silva';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Endereço por Cep');
        $this->description = $this->l('Recupera o endereço por Cep da API dos correios.');

        $this->confirmUninstall = $this->l('Tem certeza de que deseja desinstalar este módulo');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('PSMODENDERECO_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('actionValidateCustomerAddressForm') &&
            $this->registerHook('actionFrontControllerSetMedia');
    }

    public function uninstall()
    {
        Configuration::deleteByName('PSMODENDERECO_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitPsmodenderecoModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPsmodenderecoModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Configurações'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'PSMODENDERECO_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('WebService para buscar o endereço'),
                        'name' => 'PSMODENDERECO_WEB_SERVICE',
                        'class' => 't',
                        'required'  => true,
                        'is_bool' => true, 
                        'values' => array(
                            array(
                                'id' => 'viacep',
                                'value' => 'viacep',
                                'label' => $this->l('ViaCep')
                            ),
                            array(
                                'id' => 'wscorreios',
                                'value' => 'wscorreios',
                                'label' => $this->l('WsCorreios')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'PSMODENDERECO_LIVE_MODE' => Configuration::get('PSMODENDERECO_LIVE_MODE', true),
            'PSMODENDERECO_WEB_SERVICE' => Configuration::get('PSMODENDERECO_WEB_SERVICE','viacep')
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('controller') == 'AdminAddresses') {
            $this->context->controller->addJS($this->_path.'views/js/jquery.mask.min.js');
            $this->context->controller->addJS($this->_path.'views/js/front.js');
        }
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        if($this->context->controller->php_self == 'address') {
            $this->context->controller->registerJavascript(
                'module-psmodendereco-jquerymask',
                'modules/'.$this->name.'/views/js/jquery.mask.min.js',
                ['priority' => 200]
            );
            $this->context->controller->registerJavascript(
                'module-psmodendereco-front',
                'modules/'.$this->name.'/views/js/front.js',
                ['priority' => 201]
            );
        }
    }

    public function hookActionValidateCustomerAddressForm($params)
    {
        $formatedPhone = preg_replace("/[^0-9]/", "", $params['form']->getField("phone")->getValue());
        $params['form']->getField("phone")->setValue($formatedPhone);
    }

    public function getEndereco($postcode)
    {
        $this->modPostcode = preg_replace("/[^0-9]/", "", $postcode);
        if (!preg_match('/^[0-9]{8}?$/', $this->modPostcode)) {
            throw new Exception('Cep inválido');
        }
        //$this->wsViaCep();
        $this->wsCorreios();
        $this->getIdState();
        return $this->modDataAddress;
    }

    private function wsViaCep()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        $url = "http://viacep.com.br/ws/{$this->modPostcode}/json";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION,3); 
        $results_string = curl_exec($ch);
        curl_close($ch);
        $this->modDataAddress = json_decode($results_string, true);
    }

    private function wsCorreios()
    {
        $url = 'https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl';
        $options = [
            'soap_version'   => SOAP_1_1,
            'trace' => true
        ];
        $client = new SoapClient($url, $options);
        $result = $client->consultaCEP(['cep'=>$this->modPostcode]);
        $this->modDataAddress = [
            'logradouro' => $result->return->end,
            'bairro' => $result->return->bairro,
            'localidade' => $result->return->cidade,
            'uf' => $result->return->uf
        ];
    }

    private function getIdState()
    {
        $db_prefix = _DB_PREFIX_;
        $db = Db::getInstance();
        $isoCode = $this->modDataAddress['uf'];
        $sql = "SELECT id_state FROM `{$db_prefix}state` WHERE `iso_code` = '{$isoCode}'";
        $result = $db->getRow($sql);
        $this->modDataAddress['id_state'] = $result['id_state'];
    }
}
