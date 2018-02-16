<?php
namespace App\Form;

use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;



class FormArtistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add("name", TextType::class, ['attr' => ["placeholder" => "Entrez un nom"], 'required' => true]);
        $builder->add('genres', EntityType::class, [
            'label'=>'Genre(s)',
            'class'=> Genre::class,
            'choice_label'=>'name',
            'multiple'=>true,
            'attr'=>['style'=> 'height: 150px;'],
            'query_builder'=>function(EntityRepository $er){
                return $er->createQueryBuilder('g')
                    ->orderBy('g.name', 'ASC');
            },
        ]);
        $builder->add("Valider", SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }


}