<?php

return array(
    'label' => array(
        'de' => array(
            'jamp: Hintergrundbild oder -farbe START',
            '"Wrapper-Element mit Hintergrundbild oder -farbe erstellen" öffnen',
        ),
    ),
    'types' => array('content'),
    'contentCategory' => 'texts',
    'standardFields' => array('headline', 'cssID'),
    'wrapper' => array(
        'type' => 'start',
    ),
    'fields' => array(
        'backgroundcolor1' => array(
            'label' => array(
                'de' => array('Hintergrundfarbe', ''),
            ),
            'inputType' => 'text',
            'eval' => array(
                'colorpicker' => true,
                'tl_class' => 'w50',
            ),
        ),
        'backgroundcolor1_opacity' => array(
            'label' => array(
                'de' => array('Hintergrundfarbentransparenz', ''),
            ),
            'inputType' => 'text',
            'default' => 1.0,
            'eval' => array(
                'digit' => true,
                'tl_class' => 'w50',
            ),
        ),
        'image1' => array(
            'label' => array(
                'de' => array('Hintergrundbild', ''),
            ),
            'inputType' => 'fileTree',
            'eval' => array(
                'fieldType' => 'radio',
                'filesOnly' => true,
                'extensions' => 'jpg,jpeg,png,gif,svg',
                'tl_class' => 'clr',
            ),
        ),
    ),
);
