<?php

namespace Backend\Modules\Tags\Actions;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Backend\Core\Engine\Base\ActionIndex as BackendBaseActionIndex;
use Backend\Core\Engine\Authentication as BackendAuthentication;
use Backend\Core\Engine\DataGridDB as BackendDataGridDB;
use Backend\Core\Engine\DataGridDoctrine;
use Backend\Core\Engine\Language as BL;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Tags\Engine\Model as BackendTagsModel;

/**
 * This is the index-action, it will display the overview of tags
 *
 * @author Dave Lens <dave.lens@netlash.com>
 * @author Davy Hellemans <davy.hellemans@netlash.com>
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class Index extends BackendBaseActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        $this->loadDataGrid();
        $this->parse();
        $this->display();
    }

    /**
     * Loads the datagrids
     */
    private function loadDataGrid()
    {
        // create datagrid
        $this->dataGrid = new DataGridDoctrine(
            BackendTagsModel::ENTITY_CLASS,
            array(
                'language' => BL::getWorkingLanguage(),
            ),
            array('id', 'name', 'number_of_connections')
        );

        // header labels
        $this->dataGrid->setHeaderLabels(array(
            'name' => \SpoonFilter::ucfirst(BL::lbl('Name')),
            'number_of_connections' => \SpoonFilter::ucfirst(BL::lbl('Amount'))
        ));

        // sorting columns
        $this->dataGrid->setSortingColumns(array('name', 'number_of_connections'), 'number_of_connections');
        $this->dataGrid->setSortParameter('desc');

        // add the multicheckbox column
        $this->dataGrid->setMassActionCheckboxes('checkbox', '[id]');

        // add mass action dropdown
        $ddmMassAction = new \SpoonFormDropdown('action', array('delete' => BL::lbl('Delete')), 'delete');
        $ddmMassAction->setOptionAttributes('delete', array('message-id' => 'confirmDelete'));
        $this->dataGrid->setMassAction($ddmMassAction);

        // add attributes, so the inline editing has all the needed data
        $this->dataGrid->setColumnAttributes('name', array('data-id' => '{id:[id]}'));

        // check if this action is allowed
        if (BackendAuthentication::isAllowedAction('Edit')) {
            // add column
            $this->dataGrid->addColumn(
                'edit',
                null,
                BL::lbl('Edit'),
                BackendModel::createURLForAction('Edit') . '&amp;id=[id]',
                BL::lbl('Edit')
            );
        }
    }

    /**
     * Parse & display the page
     */
    protected function parse()
    {
        parent::parse();

        $this->tpl->assign('dataGrid', (string) $this->dataGrid->getContent());
    }
}
