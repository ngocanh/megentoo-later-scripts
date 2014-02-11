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
 * Class Megentoo_LaterScripts_Helper_Filter - extract and remove inline js
 *
 * @category  Megentoo
 * @package   Megentoo_LaterScripts
 * @author    Ngoc Anh Doan <ngoc@nhdoan.de>
 * @license   http://opensource.org/licenses/MIT     MIT License (MIT)
 * @link      https://github.com/ngocanh/megentoo-later-scripts
 */
class Megentoo_LaterScripts_Helper_Filter implements Zend_Filter_Interface
{
    /**
     * List of extracted inline scripts for later merged output appended to 'before body end' block.
     *
     * @var array
     */
    protected $_inlineJs = array();

    /**
     * Regex used to extract and remove inline JS scripts.
     *
     * @var string
     * @todo Implement setter and getter if necessary and extend regex to get it more robust.
     */
    protected $_inlineJsRegex = '#<script type="text\/javascript">(.*?)<\/script>#s';

    /**
     * Extracting inline JS code blocks from HTML content and collecting those.
     *
     * @param string $value     HTML content
     * @param string $blockName Block name of corresponding HTML content
     *
     * @return Megentoo_LaterScripts_Helper_Filter
     * @see self::$_inlineJs
     */
    public function extractInlineJs(&$value, $blockName = 'anonymous')
    {
        $matches = array();
        preg_match_all($this->_inlineJsRegex, $value, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $this->_inlineJs[$blockName][] = $match;
            }
        }

        return $this;
    }

    /**
     * Removing inline JS blocks from HTML content.
     *
     * @param string $value (HTML) Content to filter
     *
     * @return mixed
     */
    public function filter($value)
    {
        return preg_replace($this->_inlineJsRegex, '', $value);
    }

    public function getInlineJs()
    {
        return $this->_inlineJs;
    }
}
