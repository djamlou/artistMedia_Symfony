<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;



class FormUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add("username", TextType::class, ['attr' => ["placeholder" => "Entrez un pseudo"], 'required' => true]);
        $builder->add("password", PasswordType::class, ['attr' => ["placeholder" => "Entrez un mot de passe"], 'required' => true]);
        $builder->add("email", EmailType::class, ['attr' => ["placeholder" => "Entrez une adresse mail"], 'required' => true]);
        $builder->add("Valider", SubmitType::class, array('attr' => array('class' => 'btn btn-primary')));
    }


}