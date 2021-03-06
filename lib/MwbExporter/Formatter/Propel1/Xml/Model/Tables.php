<?php
/*
 * The MIT License
 *
 * Copyright (c) 2010 Johannes Mueller <circus2(at)web.de>
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

namespace MwbExporter\Formatter\Propel1\Xml\Model;

use MwbExporter\FormatterInterface;

use MwbExporter\Model\Tables as BaseTables;
use MwbExporter\Helper\Pluralizer;
use MwbExporter\Writer\WriterInterface;
use MwbExporter\Formatter\Propel1\Xml\Formatter;

class Tables extends BaseTables
{
    /**
     * Write document as generated code.
     *
     * @param \MwbExporter\Writer\WriterInterface $writer
     * @return \MwbExporter\Formatter\Propel1\Xml\Model\Table
     */
    public function write(WriterInterface $writer)
    {
        $writer->open($this->getDocument()->getConfig()->get(Formatter::CFG_FILENAME));
        $this->writeTables($writer);
        $writer->close();
        return $this;
    }

    /**
     * Write document as generated code.
     *
     * @param \MwbExporter\Writer\WriterInterface $writer
     * @return \MwbExporter\Formatter\Propel1\Xml\Model\Tables
     */
    public function writeTables(WriterInterface $writer)
    {
        $writer
            ->write('<?xml version="1.0" encoding="UTF-8"?>')
            ->write('<database name="%s" defaultIdMethod="native"', $this->getSchema()->getName())
            ->indent()
            ->write('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"')
            ->write('xsi:noNamespaceSchemaLocation="http://xsd.propelorm.org/1.6/database.xsd" >')
            ->outdent()
        ;
        foreach ($this->tables as $table) {
            $table->write($writer);
        }
        $writer->write('</database>');
        return $this;
    }
}