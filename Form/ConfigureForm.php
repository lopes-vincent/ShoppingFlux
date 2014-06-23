<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace ShoppingFlux\Form;
use ShoppingFlux\Model\ShoppingFluxConfigQuery;
use ShoppingFlux\ShoppingFlux;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\LangQuery;
use Thelia\Model\Module;
use Thelia\Model\ModuleQuery;
use Thelia\Module\AbstractDeliveryModule;

/**
 * Class ConfigureForm
 * @package ShoppingFlux\Form
 * @author Benjamin Perche <bperche@openstudio.fr>
 */
class ConfigureForm extends BaseForm
{
    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $lang = LangQuery::create()
            ->findOneByLocale($this->getRequest()->getPreferredLanguage())
            ;

        if($lang === null) {
            throw new \ErrorException("The locale ".$this->getRequest()->getPreferredLanguage()." doesn't exist");
        }

        $langsId = LangQuery::create()
            ->select("Id")
            ->find()
            ->toArray();

        $deliveryModulesId = ModuleQuery::create()
            ->filterByType(AbstractDeliveryModule::DELIVERY_MODULE_TYPE)
            ->select("Id")
            ->find()
            ->toArray()
        ;

        $translator = Translator::getInstance();
        $this->formBuilder
            ->add("token", "text", array(
                "label" => $translator->trans("ShoppingFlux Token", [], ShoppingFlux::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "shopping_flux_token"],
                "constraints" => [new NotBlank()],
                "required"  => true,
                "data" => ShoppingFluxConfigQuery::getToken(),
            ))
            ->add("prod", "checkbox", array(
                "label" => $translator->trans("In production", [], ShoppingFlux::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "shopping_flux_prod"],
                "required" => false,
                "data" => ShoppingFluxConfigQuery::getProd(),
            ))
            ->add("delivery_module_id", "choice", array(
                "label" => $translator->trans("Delivery module", [], ShoppingFlux::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "shopping_flux_delivery_module_id"],
                "required" => true,
                "multiple" => false,
                "choices" => $deliveryModulesId,
                "data" => ShoppingFluxConfigQuery::getDeliveryModule()->getId(),
            ))
            ->add("lang_id", "choice", array(
                "label" => $translator->trans("Language", [], ShoppingFlux::MESSAGE_DOMAIN),
                "label_attr" => ["for" => "shopping_flux_lang"],
                "required" => true,
                "choices" => $langsId,
                "data" => $lang->getId(),
            ))
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "configure_shopping_flux_form";
    }

} 