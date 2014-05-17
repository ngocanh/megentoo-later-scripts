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
 * @author    Ngoc Anh Doan <ngoc@nhdoan.de>, Tegan Snyder <tsnyder@tegdesign.com>
 * @license   http://opensource.org/licenses/MIT     MIT License (MIT)
 * @link      https://github.com/ngocanh/megentoo-later-scripts 
 *            https://github.com/tegansnyder/megentoo-later-scripts
 */
class Megentoo_LaterScripts_Block_Html_Head extends Mage_Page_Block_Html_Head
{

    public function getCssJsHtml($load_type = 'js')
    {

        // dont use this rewrite if the extension is not enabled
        if (!Mage::helper('laterscripts')->isEnabled()) {
            return parent::getCssJsHtml();
        }

        // separate items by types
        $lines  = array();
        foreach ($this->_data['items'] as $item) {

            if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
                continue;
            }
            $if     = !empty($item['if']) ? $item['if'] : '';
            $params = !empty($item['params']) ? $item['params'] : '';

            // only load css
            if ($load_type == 'css') {

                switch ($item['type']) {
                    case 'js_css':    // js/*.css
                    case 'skin_css':  // skin/*/*.css
                        $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                        break;
                }

            // only load js
            } elseif ($load_type == 'js') {

                switch ($item['type']) {
                    case 'js':        // js/*.js
                    case 'skin_js':   // skin/*/*.js
                        $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                        break;
                    default:
                        $this->_separateOtherHtmlHeadElements($lines, $if, $item['type'], $params, $item['name'], $item);
                        break;
                }

            // fallback load everything
            } else {

                // load all

                switch ($item['type']) {
                    case 'js':        // js/*.js
                    case 'skin_js':   // skin/*/*.js
                    case 'js_css':    // js/*.css
                    case 'skin_css':  // skin/*/*.css
                        $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                        break;
                    default:
                        $this->_separateOtherHtmlHeadElements($lines, $if, $item['type'], $params, $item['name'], $item);
                        break;
                }

            }

        }

        if (Mage::helper('laterscripts')->isDebug()) {
            if ($load_type == 'js') {
                Mage::log($lines, null, 'laterscripts-js.log');  
            } else {
                Mage::log($lines, null, 'laterscripts-css.log');
            }
        }

        // prepare HTML
        $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
        $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');
        $html   = '';
        
        foreach ($lines as $if => $items) {
            if (empty($items)) {
                continue;
            }
            if (!empty($if)) {
                // open !IE conditional using raw value
                if (strpos($if, "><!-->") !== false) {
                    $html .= $if . "\n";
                } else {
                    $html .= '<!--[if '.$if.']>' . "\n";
                }
            }

            // static and skin css
            $html .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />'."\n",
                empty($items['js_css']) ? array() : $items['js_css'],
                empty($items['skin_css']) ? array() : $items['skin_css'],
                $shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
            );

            if ($load_type != 'css') {

                // static and skin javascripts
                $html .= $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s"%s></script>' . "\n",
                    empty($items['js']) ? array() : $items['js'],
                    empty($items['skin_js']) ? array() : $items['skin_js'],
                    $shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
                );

                // other stuff
                if (!empty($items['other'])) {
                    $html .= $this->_prepareOtherHtmlHeadElements($items['other']) . "\n";
                }

            }

            if (!empty($if)) {
                // close !IE conditional comments correctly
                if (strpos($if, "><!-->") !== false) {
                    $html .= '<!--<![endif]-->' . "\n";
                } else {
                    $html .= '<![endif]-->' . "\n";
                }
            }
        }
        return $html;
    }

}