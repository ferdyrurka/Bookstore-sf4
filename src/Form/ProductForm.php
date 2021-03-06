<?php


namespace App\Form;

use App\Entity\Category;
use App\Form\Type\ProductImageFormType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CreateProductForm
 * @package App\Form
 */
class ProductForm extends AbstractType
{
    /**
     * @var array
     */
    private $base;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'maxlength' => 6,
                    'minlength' => 128,
                ],
            ])
            ->add('price_float', MoneyType::class, array(
                'currency' => 'PLN',
                'trim' => true
            ))
            ->add('magazine', IntegerType::class)
            ->add('upload_product_image', CollectionType::class, array(
                'entry_type' => ProductImageFormType::class,
                'entry_options' => array('label'=>false),
                'label' => false
            ))
            ->add('categories_id', EntityType::class, array(
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
                'label' => 'Categories',
                'empty_data' => null,
                'required' => false,
            ))
            ->add('description', CKEditorType::class, array(
                'config' => array(
                    'uiColor'=>'#ffffff',
                ),
                'required' => false
            ))
            ->add('save', SubmitType::class)
        ;

        $builder->get('name')->addModelTransformer(new CallbackTransformer(
            function (?string $name) {
                return $name;
            },
            function (?string $name) {
                return htmlspecialchars($name);
            }
        ));

        $this->base = array('<script>','</script>');

        $builder
            ->get('description')->addModelTransformer(new CallbackTransformer(
                function ($description) {
                    if (!empty($description)) {
                        return str_replace($this->base, '', $description);
                    }
                    return $description;
                },
                function ($description) {
                    return str_replace($this->base, '', $description);
                }
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
           'csrf_protection' => true,
           'csrf_field_name' => '_token',
           'csrf_token_id' => 'create.product.admin'
        ));
    }
}
