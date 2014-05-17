megentoo-later-scripts
======================

This Magento extension is an experimental implementation. It listens to the following event observers:
* core_block_abstract_to_html_before
* core_block_abstract_to_html_after

It extracts all Magento inline JS placing it in the footer of your store:
```app/design/frontend/base/default/template/page/html/footer_scripts.phtml```

It rewrites ```Mage_Page_Block_Html_Head``` adding new paramater to ```getCssJsHtml()``` function to specify the ```$load_type``` (css/js).
```php
// in footer_scripts.phtml
echo $this->getCssJsHtml('js') 
```
It keeps all Magento CSS in the head:
```app/design/frontend/base/default/template/page/html/head_script_less.phtml```

```php
// in head_script_less.phtml
echo $this->getCssJsHtml('css') 
```

The ```Megentoo_LaterScripts_Helper_Data``` helper has a function ```isEnabled```. You can dynamically disable the extension on certain pages. There is an example in the ```app/code/community/Megentoo/LaterScripts/Helper/Data.php``` file for disabling it on the ```checkout/onepage```.
```php
// dont use on checkout pages
if (Mage::getURL('checkout/onepage') == Mage::helper('core/url')->getCurrentUrl()) {
	return false;
} else {
	return Mage::getStoreConfigFlag(self::XML_PATH_LATERSCRIPTS_ENABLE);
}
```

### Configuration
A new section will show up in System -> Configuration -> Design called ```HTML Later Scripts``` where you can enable the extension and turn on debugging.

### Notes
Original Author:
https://github.com/ngocanh/megentoo-later-scripts
Pull Request:
https://github.com/ngocanh/megentoo-later-scripts/pull/1
