<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is our extended version of \SpoonFormFile
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Annelies Van Extergem <annelies.vanextergem@netlash.com>
 */
class FormFile extends \SpoonFormFile
{
    /**
     * Should the helpTxt span be hidden when parsing the field?
     *
     * @var    bool
     */
    private $hideHelpTxt = false;

    /**
     * Hides (or shows) the help text when parsing the field.
     *
     * @param bool $on
     */
    public function hideHelpTxt($on = true)
    {
        $this->hideHelpTxt = $on;
    }

    /**
     * Parses the html for this filefield.
     *
     * @param \SpoonTemplate $template The template to parse the element in.
     * @return string
     */
    public function parse($template = null)
    {
        // get upload_max_filesize
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        if ($uploadMaxFilesize === false) {
            $uploadMaxFilesize = 0;
        }

        // reformat if defined as an integer
        if (\SpoonFilter::isInteger($uploadMaxFilesize)) {
            $uploadMaxFilesize = $uploadMaxFilesize / 1024 . 'MB';
        }

        // reformat if specified in kB
        if (strtoupper(substr($uploadMaxFilesize, -1, 1)) == 'K') {
            $uploadMaxFilesize = substr($uploadMaxFilesize, 0, -1) . 'kB';
        }

        // reformat if specified in MB
        if (strtoupper(substr($uploadMaxFilesize, -1, 1)) == 'M') {
            $uploadMaxFilesize .= 'B';
        }

        // reformat if specified in GB
        if (strtoupper(substr($uploadMaxFilesize, -1, 1)) == 'G') {
            $uploadMaxFilesize .= 'B';
        }

        // name is required
        if ($this->attributes['name'] == '') {
            throw new \SpoonFormException('A name is required for a file field. Please provide a name.');
        }

        // start html generation
        $output = '<input type="file"';

        // add attributes
        $output .= $this->getAttributesHTML(
            array(
                '[id]' => $this->attributes['id'],
                '[name]' => $this->attributes['name']
            )
        ) . ' />';

        // add help txt if needed
        if (!$this->hideHelpTxt) {
            if (isset($this->attributes['extension'])) {
                $output .= '<span class="helpTxt">' .
                           sprintf(
                               Language::getMessage('HelpFileFieldWithMaxFileSize', 'core'),
                               $this->attributes['extension'],
                               $uploadMaxFilesize
                           ) . '</span>';
            } else {
                $output .= '<span class="helpTxt">' .
                           sprintf(
                               Language::getMessage('HelpMaxFileSize'),
                               $uploadMaxFilesize
                           ) . '</span>';
            }
        }

        // parse to template
        if ($template !== null) {
            $template->assign('file' . \SpoonFilter::toCamelCase($this->attributes['name']), $output);
            $template->assign(
                'file' . \SpoonFilter::toCamelCase($this->attributes['name']) . 'Error',
                ($this->errors != '') ? '<span class="formError">' . $this->errors . '</span>' : ''
            );
        }

        return $output;
    }
}
