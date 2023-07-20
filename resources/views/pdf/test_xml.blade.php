<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
   xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
   xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
   xmlns:ccts="urn:un:unece:uncefact:documentation:2"
   xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
   xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
   xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2"
   xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1"
   xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2"
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">

   <ext:UBLExtensions>
    <ext:UBLExtension>
        <ext:ExtensionURI>urn:oasis:names:specification:ubl:dsig:enveloped:xades</ext:ExtensionURI>
        <ext:ExtensionContent>
            <sig:UBLDocumentSignatures xmlns:sac="urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2" xmlns:sbc="urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2" xmlns:sig="urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2">
                <sac:SignatureInformation>
                    <cac:Price>
                        <cbc:PriceAmount currencyID="SAR">450</cbc:PriceAmount>
                        <cbc:BaseQuantity unitCode="PCE">1</cbc:BaseQuantity>
                        <cac:AllowanceCharge>
                            <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
                            <cbc:AllowanceChargeReason>Discount</cbc:AllowanceChargeReason>
                            <cbc:Amount currencyID="SAR">100</cbc:Amount> 
                            <cac:TaxCategory>
                                <cbc:ID>S</cbc:ID>
                                <cbc:Percent>15</cbc:Percent>
                                <cac:TaxScheme>
                                    <cbc:ID>VAT</cbc:ID>
                                </cac:TaxScheme>
                            </cac:TaxCategory>
                        </cac:AllowanceCharge>
                        <cac:TaxTotal>
                            <cbc:TaxAmount currencyID="SARSAR">870</cbc:TaxAmount>
                            <cac:TaxSubtotal>
                                <cbc:TaxableAmount currencyID="SAR">5800</cbc:TaxableAmount> 
                                <cbc:TaxAmount currencyID="SAR">870</cbc:TaxAmount> 
                                <cac:TaxCategory>
                                    <cbc:ID>S</cbc:ID>
                                    <cbc:Percent>15</cbc:Percent>
                                    <cac:TaxScheme>
                                        <cbc:ID>VAT</cbc:ID>
                                    </cac:TaxScheme>
                                </cac:TaxCategory>
                            </cac:TaxSubtotal>
                            <cac:TaxSubtotal>
                                <cbc:TaxableAmount currencyID="SAR">3000</cbc:TaxableAmount>
                                <cbc:TaxAmount currencyID="SAR">0</cbc:TaxAmount>
                                <cac:TaxCategory>
                                    <cbc:ID>E</cbc:ID>
                                    <cbc:Percent>0</cbc:Percent>
                                    <cbc:TaxExemptionReason>Reason for tax exempt</cbc:TaxExemptionReason>
                                    <cac:TaxScheme>
                                        <cbc:ID>VAT</cbc:ID>
                                    </cac:TaxScheme>
                                </cac:TaxCategory>
                            </cac:TaxSubtotal>
                        </cac:TaxTotal>
                        <!-- Invoice line with VAT 15% -->
                        <cac:InvoiceLine>
                            <cbc:ID>1</cbc:ID>
                            <cbc:Note>Testing note on line level</cbc:Note>
                            <cbc:InvoicedQuantity unitCode="PCE">10</cbc:InvoicedQuantity>
                            <cbc:LineExtensionAmount currencyID="SAR">5000.00</cbc:LineExtensionAmount>
                            <!-- code omitted for clarity -->
                            <cac:ClassifiedTaxCategory>
                                <cbc:ID>S</cbc:ID>
                                <cbc:Percent>15.00</cbc:Percent>
                                <cac:TaxScheme>
                                    <cbc:ID>VAT</cbc:ID>
                                </cac:TaxScheme>
                            </cac:ClassifiedTaxCategory>
                        </cac:InvoiceLine>

                    </cac:Price>                    
                </sac:SignatureInformation>
            </sig:UBLDocumentSignatures>
        </ext:ExtensionContent>
    </ext:UBLExtension>
</ext:UBLExtensions>
</Invoice>
