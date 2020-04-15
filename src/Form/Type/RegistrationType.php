<?php declare(strict_types=1);

namespace App\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends RegistrationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('captcha', CaptchaType::class);
    }
}