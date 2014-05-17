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
 * Class Megentoo_LaterJs_Helper_Data - standard helper
 *
 * @category  Megentoo
 * @package   Megentoo_LaterScripts
 * @author    Ngoc Anh Doan <ngoc@nhdoan.de>
 * @license   http://opensource.org/licenses/MIT     MIT License (MIT)
 * @link      https://github.com/ngocanh/megentoo-later-scripts
 */
class Megentoo_LaterScripts_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Template names for header block
     */
    const TEMPLATE_FOOTER_SCRIPTS = 'page/html/footer_scripts.phtml',
          TEMPLATE_HEAD_SCRIPT_LESS = 'page/html/head_script_less.phtml';

    /**
     * XML path to system configurations of this module
     */
    const XML_PATH_LATERSCRIPTS_DEBUG = 'design/laterscripts/debug',
          XML_PATH_LATERSCRIPTS_ENABLE = 'design/laterscripts/enable';

    /**
     * Generates 'head' block HTML having self::TEMPLATE_FOOTER_SCRIPTS template set.
     *
     * @return string
     */
    public function getFooterScriptsHtml()
    {
        $html = '';

        if (($head = Mage::app()->getLayout()->getBlock('head'))) {
            $html .= $head->setTemplate(self::TEMPLATE_FOOTER_SCRIPTS)->toHtml();
        }

        return $html;
    }

    /**
     * Getter of inline JS filter.
     *
     * @return Megentoo_LaterScripts_Helper_Filter
     */
    public function getHtmlFilter()
    {
        return Mage::helper('laterscripts/filter');
    }

    /**
     * Config flag if debug mode.
     *
     * @return bool
     */
    public function isDebug()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_LATERSCRIPTS_DEBUG);
    }

    /**
     * Config flag if enabled
     *
     * @return bool
     */
    public function isEnabled()
    {

        // dont use footer js on checkout pages
        if (Mage::getURL('checkout/onepage') == Mage::helper('core/url')->getCurrentUrl()) {
            return false;
        } else {
            return Mage::getStoreConfigFlag(self::XML_PATH_LATERSCRIPTS_ENABLE);
        }

    }
}
