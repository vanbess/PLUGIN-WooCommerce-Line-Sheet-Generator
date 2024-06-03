## PLUGIN: Product Line Sheet Generator for WooCommerce

This plugin adds the ability to generate line sheet PDFs of products which are published to a WooCommerce store.

### Requirements

WooCommerce needs to be installed for this plugin to work, and you need to have some products published as well. For multicurrency support, Currency Switcher for WooCommerce Pro needs to be installed as well.

### WordPress Admin Area

The admin page for this plugin can be found under Line Sheet Generator in the WordPress admin menu. 

There are several form fields to pay attention to when you are creating line sheets:

 1. __Select product IDs:__ As the name suggests, this field allows you to search for and select multiple products to add to your line sheet. Product names and corresponding product IDs are shown for your selection.
 2. __Select line sheet layout type:__ There are 3 layout types to choose from here, namely 3 Product Detailed Wholesale Layout (default - landscape orientation), Standard 4 Product Layout (portrait orientation) and Standard 6 Product Layout
 3. __Link products in line sheet to individual product pages?__ Whether or not you want the products you add to your line sheet to be linked to their individual product pages on the website in question
    1. __Add tracking variables below to track link clicks from line sheet (optional)__ If the above option is set to 'Yes', allows you to add tracking variables to product page links so that you can track conversions from a specific PDF line sheet. Note that you will have to handle the parsing of tracking data separately because the plugin only provides to option to add these variables to your product links and does not handle any parsing of variables which you may require.
 4. __Select Currency:__ As the label suggests, you can select a currency other than the default USD here. Note that Currency Switcher for WooCommerce Pro will need to be installed, and your currencies will need to be set up in order for this option to work. The list of selectable currencies is based on the active currencies as defined on the settings page of the currency switcher plugin mentioned. Should you select a currency other than the default, the plugin will look for custom defined per product prices for that currency, else it will convert to said currency from the default price using the predefined exchange rates as set up on the currency switcher plugin's settings page.
 5. __Specify page header text:__ As the name suggests, you can specify header (title) text for the line sheet here. This text will be shown at the top of the line sheet which is going to be generated. Note that this field has a character limit of 50 characters max so that the line sheet's layout is preserved at all times.
 6. __Specify footer contact email:__ The email address which you want to appear in the footer, clickable as a mailto link, i.e. if a user clicks on it, he/she will be redirected to an email client with the TO email address field prepopulated.
 7. __Add line sheet intro text here (optional, 250 characters max):__ As the name suggests, you can add some call to action text for your line sheet here. Note that this text will only be displayed on the first page of the generated line sheet, and only on 4 and 6 product layouts due to space and layout preservation considerations.
 8. __Line sheet save name:__ The name you would like to use for the line sheet PDF which is generated when clicking on "Generate Line Sheet". You can be as descriptive as you need to be, but please avoid using any special characters in your names. Note that the name you choose will be underscored and the timestamp at which the file was generated will be prepended to the name you define here, for example "Some Funky Line Sheet Name" entered in this field will result in a filename similar to 1686130200_Some_Funky_Line_Sheet_Name.pdf

### WordPress Admin Area - Buttons and what they do

The admin area contains several buttons, each with its own unique action:

- __Clear Preview Cache__: Clears the folder containing previously generated line sheet HTML preview templates.
- __View Previously Generated Line Sheets__: As the name suggests. Shows a popup with a list of previously generated line sheets which you can download or re-download on click.
  - __Delete All__: This button resides inside the popup which gets shown when clicking the aforementioned button. Clicking it results in all previously generated line sheet PDFs being deleted from disk, so if you want to hold on to some line sheets be sure to download them prior to clicking this button!
- __Generate Preview__: Clicking this button generates an approximate HTML preview of your line sheet, given the specifications and products defined in the previously mentioned set of fields. The line sheet preview will appear in the designated preview container at the bottom of the page.
- __Generate Line Sheet__: As the name suggests, generate your line sheet based on the last previewed line sheet HTML.

## Using the plugin

The line sheet generator follows a specific order order of events in order to accurately generate line sheet PDFs:

1.  Line sheet products and other required parameters are specified;
2.  The __Generate Preview__ button is clicked;
3.  An HTML preview of the line sheet PDF to be generated appears in the line sheet preview container;
4.  A name for the line sheet to be generated is provided in the __Line sheet save name__ field;
5.  The __Generate Line Sheet__ button is clicked, which uses the last generated line sheet preview HTML to generate a line sheet PDF, whereafter the page reloads;
6.  The generated line sheet is saved to disk and can be found at the bottom of the list of line sheet PDF links which are present in the previously generated line sheet popup which is shown when clicking on __View Previously Generated Line Sheets__. Clicking on a specific link downloads the associated PDF line sheet.

Please make sure that you follow the above set of steps to generate your line sheets so as to avoid any issues or errors.

## Product specific settings for wholesale line sheet layout

By default this plugin queries basic product info such as product title, price, SKU and so on when generating line sheets. 

For the wholesale line sheet layout type, you have the option to define an additional set of attributes per product as you see fit. 

You can do so by navigating to the product edit screen of the product in question, looking for the __Line Sheet Data__ panel and adding your additional attribute settings there. 

Note that a maximum of 5 additional attribute sets are allowed per product in order to preserve line sheet layout rules. 

Also note that standard product attributes as defined under the __Attributes__ panel on the product edit screen takes precedence over these custom attributes.

## Plugin limitations

Note that while the plugin does its best to match the look and feel of the line sheet preview HTML previously mentioned, there are limitations in terms of what kind of CSS can be successfully transferred to PDF format from HTML. 

As such you might find instances where the layout is not 100% on point with the HTML previews generated. 

While the different layout options have been thoroughly tested, it is impossible to account for every possible layout scenario, which some additional per site dev work might be required for the line sheet generator to work as expected on your website.
