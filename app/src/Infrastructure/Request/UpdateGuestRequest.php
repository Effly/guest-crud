<?php
namespace App\Infrastructure\Request;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UpdateGuestRequest extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options): void
{
$builder
->add('firstName', TextType::class, [
'constraints' => [
new Assert\NotBlank(),
new Assert\Length(['min' => 2, 'max' => 50]),
],
])
->add('lastName', TextType::class, [
'constraints' => [
new Assert\NotBlank(),
new Assert\Length(['min' => 2, 'max' => 50]),
],
])
->add('email', EmailType::class, [
'constraints' => [
new Assert\NotBlank(),
new Assert\Email(),
],
])
->add('phone', TextType::class, [
'constraints' => [
new Assert\NotBlank(),
new Assert\Length(['min' => 10, 'max' => 20]),
],
])
->add('country', TextType::class, [
'constraints' => [
new Assert\Length(['min' => 2, 'max' => 50]),
],
'required' => false,
]);
}

public function configureOptions(OptionsResolver $resolver): void
{
$resolver->setDefaults([
'csrf_protection' => false,
]);
}
}
