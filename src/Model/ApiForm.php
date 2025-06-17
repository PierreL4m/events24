<?php

namespace App\Model;
use Symfony\Component\Form\Form;

class ApiForm
{
    private Form $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

}