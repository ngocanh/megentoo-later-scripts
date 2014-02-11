<?php
/**
 * The MIT License (MIT)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @category  Megentoo
 * @package   Megentoo_LaterScripts
 * @author    Ngoc Anh Doan <ngoc@nhdoan.de>
 * @license   http://opensource.org/licenses/MIT     MIT License (MIT)
 * @link      https://github.com/ngocanh/megentoo-later-scripts
 */

/**
 * Class Megentoo_LaterScripts_Model_Observer - Observer model to hook into HTML
 * generation of blocks.
 *
 * @category  Megentoo
 * @package   Megentoo_LaterScripts
 * @author    Ngoc Anh Doan <ngoc@nhdoan.de>
 * @license   http://opensource.org/licenses/MIT     MIT License (MIT)
 * @link      https://github.com/ngocanh/megentoo-later-scripts
 */
class Megentoo_LaterScripts_Model_Observer
{
    /**
     * Special block names:
     *
     *  - 'head' block to set 'head_script_less.phtml' template
     *  - 'before_body_end' moving scripts and inline scripts to bottom
     */
    const BLOCK_NAME_BEFORE_BODY_END = 'before_body_end',
          BLOCK_NAME_HEAD = 'head';

    /**
     * Template for head block 'page/html/head_script_less.phtml'
     */
    const TEMPLATE_HEAD_SCRIPT_LESS = Megentoo_LaterScripts_Helper_Data::TEMPLATE_HEAD_SCRIPT_LESS;

    /**
     * Filter for processing block HTML to extract inline JS.
     *
     * @var Megentoo_LaterScripts_Helper_Filter
     */
    protected $_filter = null;

    /**
     * Flag indicating that head block was processed / template has been set.
     *
     * @var bool
     */
    protected $_headTemplateIsSet = false;

    /**
     * Active flag
     *
     * @var null|bool
     */
    protected $_isEnabled = null;

    /**
     * List of block names which should not been filtered/processed.
     *
     * @var array
     */
    protected $_noFilterBlockNames = array(
        'root', 'head', 'core_profiler',
    );

    /**
     * Initialize filter and set active flag
     */
    public function __construct()
    {
        $this->_isEnabled = Mage::helper('laterscripts')->isEnabled();
        $this->_filter = Mage::helper('laterscripts')->getHtmlFilter();
    }

    /**
     * 'core_block_abstract_to_html_after' event listener to filter inline scripts
     * and append those to the 'before body end' block
     *
     * @param Varien_Event_Observer $observer
     *
     * @event core_block_abstract_to_html_after
     *
     * @return void
     */
    public function filterBlocks($observer)
    {
        if (!$this->_isEnabled) {
            return;
        }

        $blockName = $observer->getBlock()->getNameInLayout();
        if ($blockName == self::BLOCK_NAME_BEFORE_BODY_END)
        {
            $transport = $observer->getTransport();
            $transport->setHtml($transport->getHtml() . "\n" . Mage::helper('laterscripts')->getFooterScriptsHtml());
        } else if (!in_array($blockName, $this->_noFilterBlockNames) && !Mage::app()->getRequest()->isAjax()) {
            $transport = $observer->getTransport();
            $html = $transport->getHtml();
            $html = $this->_filter->extractInlineJs($html, $blockName)->filter($html);
            $transport->setHtml($html);
        }
    }

    /**
     * 'core_block_abstract_to_html_before' event listener to set modified head template 'head_script_less.phtml' for
     * the head block.
     *
     * @param Varien_Event_Observer $observer
     *
     * @event core_block_abstract_to_html_before
     *
     * @return void
     */
    public function setHeadTemplate($observer)
    {
        if (!$this->_isEnabled || $this->_headTemplateIsSet) {
            return;
        }

        $block = $observer->getBlock();
        if ($block->getNameInLayout() == self::BLOCK_NAME_HEAD)
        {
            $this->_headTemplateIsSet = true;

            $block->setTemplate(self::TEMPLATE_HEAD_SCRIPT_LESS);
        }
    }
}
