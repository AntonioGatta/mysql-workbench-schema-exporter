<?php

/*
 * The MIT License
 *
 * Copyright (c) 2012 Allan Sun <sunajia@gmail.com>
 * Copyright (c) 2012 Toha <tohenk@yahoo.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace MwbExporter\Formatter\Sencha\ExtJS3\Model;

use MwbExporter\Model\Column as BaseColumn;
use MwbExporter\Helper\JSObject;
use MwbExporter\Helper\ZendURLFormatter;

class Column extends BaseColumn
{
    public function getAsField()
    {
        return new JSObject(array('name' => $this->getColumnName(), 'type' => $this->getDocument()->getFormatter()->getDatatypeConverter()->getType($this)));
    }

    public function getAsColumn()
    {
        return new JSObject(array('header' => ucwords(str_replace('_', ' ', $this->getColumnName())), 'dataIndex' => $this->getColumnName()));
    }

    public function getAsFormItem()
    {
        $result = array();
        // @see http://docs.sencha.com/ext-js/3-4/#!/api/Ext.form.ComboBox-cfg-hiddenName
        if ($this->local) {
            $result['hiddenName'] = $this->getColumnName();
        } else {
            $result['name'] = $this->getColumnName();
        }
        $anchor = null;
        switch (true) {
            case $this->isPrimary():
                $type = 'hidden';
                break;

            case $this->getColumnType() === 'com.mysql.rdbms.mysql.datatype.datetime':
            case $this->getColumnType() === 'com.mysql.rdbms.mysql.datatype.timestamp':
                $type = 'xdatetime';
                break;

            case $this->getColumnType() === 'com.mysql.rdbms.mysql.datatype.tinytext':
            case $this->getColumnType() === 'com.mysql.rdbms.mysql.datatype.mediumtext':
            case $this->getColumnType() === 'com.mysql.rdbms.mysql.datatype.longtext':
            case $this->getColumnType() === 'com.mysql.rdbms.mysql.datatype.text':
                $type = 'htmleditor';
                $anchor = '100%';
                break;

            case $this->local !== null:
                $type = 'combo';
                break;

            default:
                $type = 'textfield'; 
        }
        $result['xtype'] = $type;
        $result['fieldLabel'] = ucwords(str_replace('_', ' ', $this->getColumnName()));
        $result['allowBlank'] = $this->parameters->get('isNotNull') == 1 ? false : true;
        if ($anchor) {
            $result['anchor'] = $anchor;
        }
        if (null !== $this->local) {
            $result['valueField'] = $this->local->getForeign()->getColumnName();
            $result['displayField'] = $this->local->getReferencedTable()->getRawTableName();
            $result['mode'] = 'local';
            $result['forceSelection'] = true;
            $result['triggerAction'] = 'all';
            $result['listeners'] = array('afterrender' => new JSObject('function() {this.store.load();}', true));
            $result['store'] = new JSObject(sprintf('new Ext.data.JsonStore(%s);',
                new JSObject(array(
                    'id'     => str_replace(' ', '', ucwords(str_replace('_',' ',$this->local->getReferencedTable()->getRawTableName()))).'Store',
                    'url'    => ZendURLFormatter::fromUnderscoreConnectionToDashConnection($this->local->getReferencedTable()->getRawTableName()),
                    'root'   => 'data',
                    'fields' => array('id', 'name'),
                ))
            ), true);
        }

        return new JSObject($result);
    }
}