<?php
namespace App\Form;

use App\Entity\Artist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class FormAlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title', TextType::class, [
            'label'=>'Titre',
            'attr'=> ['maxlength'=> 100],])
            ->add('releaseDate', NumberType::class, [
                'label'=> 'Date de sortie',
            ])
            ->add('artist', EntityType::class, [
                'label'=>'Artist(s)',
                'class'=> Artist::class,
                'choice_label'=>'name',
                'query_builder'=>function(EntityRepository $er){
                    return $er->createQueryBuilder('g')
                        ->orderBy('g.name', 'ASC');
                },
            ])
            ->add('valider', SubmitType::class,  array('attr' => array('class' => 'btn btn-primary')));
    }


}