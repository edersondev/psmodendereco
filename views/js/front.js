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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$(function(){

  var urlGetEndereco = `${window.location.origin}/index.php?fc=module&module=psmodendereco&controller=endereco`;

  options = {
    onComplete: function(postcode) {
      var element = $('input[name="postcode"]');
          elementFormGroup = element.closest('div.form-group')
          elementDiv = element.parent('div');
      $.ajax({
        url: urlGetEndereco,
        type: 'post',
        dataType: 'json',
        data:{
          postcode: postcode
        },
        beforeSend: function(jqXHR,settings){
          element.attr('readonly','readonly');
          elementFormGroup.find('div.form-control-comment').html('Aguarde...');
        },
        complete: function(jqXHR,textStatus){
          element.removeAttr('readonly');
          elementFormGroup.find('div.form-control-comment').empty();
        },
        success: function(){
          elementFormGroup.addClass('has-success');
        },
        error: function(jqXHR,textStatus,errorThrown){
          var message = `
            <div class="help-block">
              <ul>
                <li class="alert alert-danger">${jqXHR.responseJSON.error}</li>
              </ul>
            </div>
          `;
          elementFormGroup.addClass('has-error');
          elementDiv.append(message);
        },
      });
    }
  };
  $('input[name="postcode"]').mask('00000-000', options);

  var maskBehavior = function (val) {
    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
  },
  options = {
    onKeyPress: function(val, e, field, options) {
      field.mask(maskBehavior.apply({}, arguments), options);
    }
  };
  $('input[name="phone"]').mask(maskBehavior,options);

});

function resetStatusField()
{
	var elementFormGroup = $('input[name="postcode"]').closest('div.form-group');
	elementFormGroup.removeClass('has-error');
	elementFormGroup.removeClass('has-success');
	elementFormGroup.find('div.help-block').remove();
}