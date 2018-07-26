<?php

class Limex_Customerlog_Model_Sysconfig_Options
{
    /**
     * Options getter - creates a list of options.
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array('value' => 0, 'label' => 'Inactive');
        $options[] = array('value' => 1, 'label' => 'Active');

        return $options;
    }
}
