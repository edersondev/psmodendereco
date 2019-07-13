{*
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
*}

<div class="panel">
	<h3><i class="icon icon-credit-card"></i> {l s='Endereço por Cep' mod='psmodendereco'}</h3>
	<p>
		<strong>{l s='Módulo para completar o endereço pelo CEP' mod='psmodendereco'}</strong><br />
		{l s='O módulo possui dois Webservices para a busca do endereço e cada um precisa de um módulo do php instalado no servidor.' mod='psmodendereco'}<br />
		{l s='Para usar o webservice do ViaCep precisa do módulo Curl do php.' mod='psmodendereco'}<br />
		{l s='Para usar o webservice dos Correios precisa do módulo Soap do php.' mod='psmodendereco'}
	</p>
	<br />
	<p>
		{l s='O formulário de endereço do cliente é configurado no seguinte caminho:' mod='psmodendereco'}
	</p>
	<p>
		<a href="index.php?controller=AdminCountries&id_country=58&updatecountry&token={$token}" title="Configuração do formulário de endereços">
			Menu International >> Localizações na aba Países.
		</a>
	</p>
</div>
