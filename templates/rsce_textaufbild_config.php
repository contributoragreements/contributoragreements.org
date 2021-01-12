<?php

return array(
	'label' => array(
		'de' => array(
			'jamp: Text auf einem Bild',
			'Stellt eine Eingabemaske mit einem Text und Bild bereit',
		),
	),
	'types' => array('content'),
	'contentCategory' => 'texts',
	'standardFields' => array('headline', 'cssID'),
	'fields' => array(
		'text1' => array(
           'label' => array('Text', 'Bitte geben Sie hier den Text ein.'),
           'inputType' => 'textarea',
           'eval' => array('rte' => 'tinyMCE')
        ),
		'image1' => array(
            'label' => array(
                'de' => array('Bild', ''),
            ),
            'inputType' => 'fileTree',
            'eval' => array(
                'fieldType' => 'radio',
                'filesOnly' => true,
                'extensions' => 'jpg,jpeg,png,gif,svg',
            ),
        ),
        'imageTitle1' => array(
            'label' => array(
                'de' => array('Bildtitel', ''),
            ),
            'inputType' => 'text',
            'eval' => array(

            ),
        ),
        'imageAsBackground1' => array(
            'label' => array(
                'de' => array('Bild als Hintergrund verwenden', ''),
            ),
            'inputType' => 'checkbox',
        )
	),
);
