# CHANGELOG
# 1.38.0
* Fix          - Fix WooCommerce Subscription [#756](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/756)
* Improvement  - Update lib js version [#755](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/755)
* Improvement  - Quality/fix e2 e tests intermittent [#752](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/752)
* Improvement  - Add payment method validation to avoid EBANX warnings on other methods [#751](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/751)
* Feature      - Add Argentine DNI Document on Checkout [#748](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/748)

# 1.37.3
* Improvement - Using WP_Mock in unit test and remove wc_function for mock [#746](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/746)

# 1.37.2
* Improvement - Remove iof when request a payment using benjamin [#738](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/738)

#  1.37.1
* Improvement - Hide optional fields tag from checkout [#740](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/740)

# 1.37.0
* Feature - Add User Agent header usage to send plugin-version data [#734](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/734)
* Feature - Add plugin check page [#735](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/735)
* Improvement - Remove PDF button and embed voucher in mobile for Banktransfer [#733](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/733)

# 1.36.0
* Fix - Resolved wrong behavior with CPF and CPNJ inputs exhibition [#729](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/729)
* Feature - Add credit card validation to front-end layer [#731](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/731)

#1.35.0
* Feature - BankTransfer payment method [#728](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/728)

# 1.34.7
* Fix - Hide document field if not required [#722](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/722)

# 1.34.6
* Fix - Resolved wrong currency in payment by link [#720](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/720)

# 1.34.5
* Improvement - Add tests for payment adapter service [#716](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/716)

# 1.34.4
* Improvement - Add instalments and interest translation [#714](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/714)

# 1.34.3
* Improvement - Mask document in Chile and Colombia [#708](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/708)
* Fix - Resolved wrong amount in checkout page [#709](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/709)
* Update - Read instalments from benjamin [#710](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/710)

# 1.34.2
* Fix - Show correct value without taxes correctly on Credit Card gateways when flag is disabled [#703](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/703)

# 1.34.1
* Fix - Show value without taxes correctly when flag is disabled
* FIx - Resolved error message on product page for countries without creditcard [#701](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/701)

# 1.34.0
* Feature - Use Benjamin to calculate instalments values [#699](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/699)
* Feature - It is now possible to configure different interest rates fees for each country that accepts credit card [#700](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/700)

# 1.33.1
* Fix - Document will not be mandatory when a gateway that is not from EBANX is selected [#697](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/697)

# 1.33.0
* Fix - PHP Strict Standards issue on WC_EBANX_Logger [687](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/687)
* Fix - Bugfix undefined offset 1 in class wc ebanx environment [692](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/692)
* Feature - Change API urls [696](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/696)

# 1.32.1
* Fix - Make chilean document mandatory [#695](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/695)

# 1.32.0
* Feature - Add document as mandatory for all colombian gateways [#694](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/694)

# 1.31.3
* Fix - Credit card tokenization compatibility issue with jQuery [#683](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/683)

# 1.31.2
* Fix - Log table creation [#679](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/679)
* Fix - Fix iframe voucher resize on thank you page [#680](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/680)

# 1.31.1
* Fix - Gather compliance data differently for payment by link [#678](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/678)

# 1.31.0
* Feature - Use Benjamin to decide if a gateway should be visible on checkout [#675](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/675)
* Fix - Complete payment status of downloadable products no longer overriden [#677](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/677)

# 1.30.0
* Feature - Force document on Argentina to have 11 digits [#670](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/670)
* Fix - Corrected bug that made some thank you pages to render html tags as texts [#671](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/671)
* Fix - Only send leads to EBANX on this plugin update to avoid errors [#672](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/672)
* Fix - Set payment as complete also on notification arrival [#673](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/673)

# 1.29.3
* Fix - get_country on null and min_instalment_value_ not defined bug [#669](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/669)

# 1.29.2
* Fix - Runtime exception warning [#663](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/663)

# 1.29.1
* Fix - Removed flush_rewrite from logs persistence step [#660](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/660)
* Fix - Restructured logs table [#661](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/661)

# 1.29.0
* Feature - Logging request data for debugging purposes [#627](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/627)
* Feature - WC Subscriptions support  [#594](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/594)
* Feature - Use benjamin EBANX's new SDK to make requests [#652](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/652)
* Feature - Get possible exceptions on apply_filters calls [#653](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/653)

# 1.28.3
* Feature - Allow 4 digit cvv only for America Express Credit Card brand [#648](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/648)
* Fix - Compatibility layer works with php 5.4 [#645](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/645)
* Fix - Currency options title not hiding when clicke on admin [#647](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/647)

# 1.28.2
* Fix - Instalment selection returning to 1x on checkout errors [#639](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/639)
* Fix - Outdated error messages [#641](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/641)

# 1.28.1
* Fix - Get settings from gateway and not from inexistent attribute on one-click [#632](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/632)

# 1.28.0
* Feature - Removed documents from some payment methods of Chilean and Colombian [#617](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/617)
* Feature - Added document as mandatory to Peruvian gateways [#618](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/618)
* Feature - Added document as mandatory to Argentinian gateways [#619](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/619)
* Feature - Added sandbox mode warning on gateway form [#624](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/624)
* Fix - Fixed due date options not appearing on admin [#616](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/616)
* Fix - Fixed instalment changing exchange rate message to total local amount message [#623](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/623)
* Fix - Fixed one click redirecting to product page on Mexico [#626](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/626)
* Fix - Stop changing 'complete' status on notification arrival [#629](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/629)

# 1.27.0
* Feature - Change tooltip message for IOF on local amount [#611](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/611)
* Feature - Show exchange rate on checkout [#612](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/612)

# 1.26.0
* Feature - Added instalments to payment by link order form [#608](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/608)
* Feature - Treating BP-R-32 error message on payment by link order form [#609](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/609)

# 1.25.2
* Fix - Fixed IOF being mistankenly applied to instalments [#606](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/606)

# 1.25.1
* Fix - Preventing IOF from being applied more than once on instalments [#604](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/604)

# 1.25.0
* Feature - Creditcard for Argentina [#602](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/602)
* Feature - SafetyPay for Ecuador [#603](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/603)

# 1.24.1
* Fix - Made some credit cards errors more specific for better user understanding [#601](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/597)

# 1.24.0
* Feature - Updated Chile payments api identification code [#597](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/597)
* Feature - Skipping asynchronous confirmations for credit card payments [#592](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/592)

# 1.23.0
* Feature - One click form can now be submitted using keyboard [#588](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/588)
* Feature - Better payment origin identification on dashboard [#590](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/590)
* Feature - Plugin approved for wordpress 4.9 [#593](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/593)
* Feature - Change minimum instalment value to 5 BRL for brazilian credit card payments [#595](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/595)

# 1.22.0
* Feature - Explicit capture button [#578](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/578)
* Feature - Added new Mexico payment method SPEI [#581](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/581)
* Fix - Adjusted iFrame display for Argentina cash payment methods [#580](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/580)
* Fix - Ignored IOF in instalments for disabled tax flag [#584](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/584)

# 1.21.0
* Feature - Added new Argentina payment method Efectivo [#576](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/576)
* Feature - Showing instalments on local currency [#571](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/571)
* Fix - Saving customer document when creating account on checkout [#566](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/566)
* Fix - Show one click button only if customer has document [#567](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/567)
* Fix - Removed ::class from same file that checks if php version is supported [#568](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/568)
* Fix - Added fake birthdate to payment data to prevent error on unconfigured merchants [#569](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/569)
* Fix - Overwriting libjs invalid expiry date error [#572](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/572)
* Fix - Saving document on redirect payment methods [#574](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/574)
* Fix - Fixed iframe resizer [#575](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/575)

# 1.20.0
* Feature - Dashboard lead links update to Dashboard v2 [#564](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/564)

# 1.19.0
* Feature - Credit Card refusal message and detailed log [#560](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/560)
* Feature - Removed Birth Date from checkout form [#561](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/561)
* Fix - Changed currency array for One Click [#562](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/562)

# 1.18.0
* Feature - Filter to change amount per gateway [#555](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/555)
* Feature - Added "Cancel Order" button on "My Account" page [#556](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/556)
* Fix - Removed autocomplete from cvv field on on-click payment [#557](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/557)
* Fix - Correctly saves Credit Card when creating account [#558](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/558)

# 1.17.1
* Fix - Removed unused include [#554](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/554)

# 1.17.0
* Feature - New Thank You pages for Credit Card and Boleto [#544](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/544)
* Feature - Added translation for Minimum Amount for Purchase error on Colombia [#547](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/547)
* Feature - Added Portuguese translation for Settings Page [#549](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/549)
* Feature - Changed project license to Apache v2.0 [#548](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/548)
* Fix - Changed IOF messages on Admin Dashboard [#545](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/545)
* Fix - Fixed Debit Card tokenize error [#550](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/550)
* Fix - Fixed One Click Payment not processing [#551](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/551)
* Fix - Updated pay for order layout [#552](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/552)

# 1.16.0
* Feature - Pay for order(woocommerce native payment by link) [#531](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/531)
* Feature - Added Credit Card Gateway for Colombia [#534](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/534)
* Feature - Added Multicaja Gateway for Chile [#539](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/539)
* Feature - Added Webpay Gateway for Chile [#538](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/538)
* Feature - Added an option for merchant to hide IOF [#541](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/541)
* Feature - Added a compability layer to prevent incompabilities from third party plugins and themes like WooCommerce's Storefront  [#535](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/535)
* Fix - Fixed PHP notice when using empty interest rate option [#530](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/530)
* Fix - Fixed error when a Merchant Payment Code gets greater than 40 characters [#537](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/537)

# 1.15.0
* Feature - Created an option to hide the local amount value on checkout page [#526](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/526)
* Fix - Applied box-sizing: border-box on boleto thank you pages button to avoid issues [#527](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/527)

# 1.14.1
* Fix - Problem resolved when the actions were updated via book actions. [#523](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/523)

# 1.14.0
* Feature - Added EURO conversion [#520](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/520)
* Feature - Support for pt_PT translations [#521](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/521)

# 1.13.2
* Fix - Replaced wp_die to exit to avoid error 500 [#515](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/515)
* Fix - Updating order when it receives a payment status notification [#516](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/516)

# 1.13.1
* Fix - Avoid duplication payment notifications [#509](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/509)
* Fix - Changed PSE thank you page HTML [#512](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/512)
* Fix - Changed Boleto thank you page HTML [#513](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/513)

# 1.13.0
* Fix - Fix for debug log when is enabled before record a log [#507](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/507)
* Fix - Fix issue to avoid some issues on refund transactions [#506](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/506)
* Fix - Changed label to Minimum Instalment (title-cased labels) [#500](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/500)
* Fix - Fixed compliance fields when country is empty [#498](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/498)
* Feature - Docker implementation and end-to-end tests for Brazil payments done [#504](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/504)

# 1.12.1
* Fix - Credit-card saving for new customers [#496](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/496)
* Fix - One-click payments button in product details [#496](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/496)

# 1.12.0
* Feature - Using interest rate on minimum instalment value [#490](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/490)
* Feature - Refactor EBANX query router [#487](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/487)
* Feature - Added a minimal value setting on settings [#477](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/477)
* Feature - Changed cookie to localStorage to save flags [#476](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/476)
* Feature - Plugin docs using phpDocumentator [#488](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/488)
* Fix - Thank you page values and instalments fixed [#473](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/473)
* Fix - Hide saved cards when option is disabled [#475](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/475)
* Fix - DNI field is not mandatory for colombia any more [#486](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/486)
* Fix - Changed the assets path to system path instead of host path [#489](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/489)
* Fix - Using absolute path to spinner gif [#485](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/485)
* Improvement - Updated notification notices and notes [#468](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/468)

# 1.11.4
* Fix - Fixed float values not being accepted in interest rates [#480](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/480)
* Fix - Added '/' to Notification URL to prevent Response Code 301 [#480](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/480)

# 1.11.3
* Fix - Fixed a problem that was incrementing the previous value by instalment [#463](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/463)

# 1.11.2
* Fix - Fixed translation paths [#462](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/462)
* Fix - Fixed converted value message when instalments is changed [#462](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/462)
* Fix - Fixed problems with newer version of WooCommerce [#462](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/462)

# 1.11.0
* Feature - Showing the prices with IOF for Brazil before on gateways [#441](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/441)
* Feature - Alert the merchants when HTTPS isn't present [#427](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/427)
* Feature - Show a message to fill the integration keys when empty [#426](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/426)
* Feature - Hooks implemented to facilitate the future integrations [#423](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/423)
* Feature - Capture payment manually clicking on "Processing" button [#421](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/421)
* Feature - Show a message when credit card is invalid on sandbox mode [#420](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/420)
* Feature - Created a flash message management helper class [#414](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/414)
* Improvement - Assets optimization by 62% faster [#429](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/429)
* Fix - Refactored and fixed bugs of one click feature [#457](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/457)
* Fix - Reverts the WC3 update keeping backward compatibility [#455](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/455)
* Fix - SafetyPay Notices [#450](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/450)

These are the most importante fixes and features, but another fixes and quality issues were resolved too.

# 1.10.1
* Fix - Removed methods to prevent fatal error [#412](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/412)

# 1.10.0
* Feature - Removed restriction on guest users for sandbox mode [#406](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/406)
* Feature - Showing some EBANX order details on admin order details page [#404](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/404)
* Improvement - Removed unecessary properties and variables [#407](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/407)
* Improvement - Improved texts and options on OXXO thank you page [#409](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/409)
* Fix - Updated deprecated function [#403](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/403)


# 1.9.1
* Fix - Fixed translations string keys in instalment template [#402](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/402)

# 1.9.0
* Feature - Advanced options hide when not applicable [#391](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/391)
* Feature - Translated my-account credit card section [#398](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/398)
* Feature - Added tooltips with nice descriptions to gateway settings page [#400](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/400)
* Improvement - Cached last key check response to speed up admin panel [#396](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/396)
* Improvement - Cached exchange rates in short intervals to improve checkout page performance [#399](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/399)
* Fix - Fixed translations for instalments with interests [#395](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/395)

# 1.8.1
* Fix - Fixed instalment reading on checkout [#393](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/393)

# 1.8.0
* Feature - Hide irrelevant fields and group fields by country on EBANX Settings page [#373](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/373)
* Feature - Added new payment gateway Baloto (Colombia) [#371](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/371)
* Feature - Hide the payment gateways on checkout page when sandbox mode is enabled for non admin users and not logged users [#380](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/380)
* Feature - A warning was added when sandbox mode is enabled [#378](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/378)
* Feature - Added asterisk to required compliance fields on checkout page [#370](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/370)

## 1.7.1
* Fix - Fixed Oxxo and Pagoefectivo iframe not showing [#382](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/382)

## 1.7.0
* Feature - The HTML select fields are now using the `select2` jQuery plugin to improve the user experience [#356](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/356)
* Improvement - We removed some unnecessaries folders and files from plugin [#353](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/353)
* Improvement - All JS assets are loading on footer [#357](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/357)
* Fix - Fixed the low resolution of the EBANX badge on non-retina displays [#354](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/354)
* Fix - Prevent fatal error when the plugin is activated without WooCommerce plugin [#360](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/360)
* Fix - Avoid SSL warning from EBANX PHP libray when the plugin make a request to URLs with a bad SSL certificate [#362](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/362)
* Fix - Resolves fatal error when the plugin can't get some informations [#365](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/365)

## 1.6.1
* Fix - Address splitting function to avoid mistakes during checkout [#352](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/352)

## 1.6.0
* Feature - Integrates with EBANX Dashboard plugin presence check [#348](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/348)
* Improvement - Gets the banking ticket HTML by cUrl with url fopen fallback [#345](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/345)
* Improvement - Changed iframe boleto URL fetching to avoid xss injections [#345](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/345)
* Fix - Max instalment limits are now adjusted for local currency instead of assuming USD for prices [#349](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/349)

## 1.5.3
* Fix - In case user country was not set one-click payments was crashing [#343](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/343)

## 1.5.2
* Fix - Checking for new feature's settings presence to avoid notices [#342](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/342)

## 1.5.1
* Fix - Notification URL in payment payload [#341](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/341)

## 1.5.0
* Feature - Instalment interest rates are now configurable [#336](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/336)
* Improvement - Payment Options section in admin is now togglable [#336](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/336)

## 1.4.1
* Fix - Fixed API Lead URL to the correct URL, because it was causing a redirect without www

## 1.4.0
* Improvement - Sending analytics information for plugin activations [#332](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/332)
* Fix - Fixed max instalments limit according to acquirer in one-click payments [#334](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/334)

## 1.3.0
* Feature - Allowed local currency, USD and EUR to be processed by EBANX based on WooCommerce Currency Options [#325](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/325)
* Improvement - Updated to new EBANX logo [#326](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/326)
* Fix - Removed the pipe character from the last WooCommerce Checkout Settings tab menu [#329](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/329)

## 1.2.3
* Fix - Checkout manager field for person type selecting in Brasil value is now respected [#323](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/323)

## 1.2.2
* Fix - Chceckout manager fields are no longer mandatory when activated [#320](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/320)

## 1.2.1
* Fix - Chile payments when using checkout manager [#306](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/306)

## 1.2.0
* Feature - Instalments limit based on minimun amount accepted by credit card acquirer [#298](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/298)
* Feature - API requests now using cUrl as main method of http communication [#302](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/302)
* Feature - Checkout manager option for entity type field in brazil checkout in cases where cnpj and cpf are both enabled [#304](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/304)
* Fix - Undisplayed thank-you-page messages [#299](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/299)
* Fix - Checkout manager settings being respected even when disabled [#304](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/304)

## 1.1.2
* Fix - Integration keys validation messages now update properly [#297](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/297)

## 1.1.1
* Fix - Brazil compliance fields showing for other countries [#294](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/294)

## 1.1.0
* Feature - Instalments field now gets hidden when max instalments is set to one [#275](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/275)
* Feature - Send store notification and return links to payment api [#268](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/268)
* Feature - Support for third-party checkout manager plugins [#279](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/279)
* Feature - CPF/CNPJ Brazilian person types support [#279](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/279)
* Feature - New debit card flags for mexico [#290](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/290)
* Change - Added the new tags: `alternative payments` and `accept more payments`
* Fix - Thank you pages for each payment gateway are now called by order status [#277](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/277)
* Fix - The credit cards gateways were separated by countries [#277](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/277)

## 1.0.2
* Bug - Fixed bug that was breaking the media uploader [#267](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/267)
* Enhancement - All methods are commented now [#266](https://github.com/ebanx/woocommerce-gateway-ebanx/pull/266)

## 1.0.1
* 2016-01-17 - Texts - Chaging the namings and texts from plugin.

## 1.0.0
* 2016-12-30 - First Release.
