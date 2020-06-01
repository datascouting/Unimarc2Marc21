<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:marc="http://www.loc.gov/MARC21/slim" version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform" exclude-result-prefixes="marc">
    <xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>

    <!--

  Transformation from UNIMARC XML representation to MARCXML.
  Based upon http://www.loc.gov/marc/unimarctomarc21.html [1]

  - v1.0
         Initial version of the file based on https://github.com/edsd/biblio-metadata/blob/master/UNIMARC2MARC21.xsl
  - v2.0 (by Theodoros Theodoropoulos)
         Fixed/Enhanced translation of Unimarc 100 to MARC 008 field
         Added translation of many more fields.
  - v2.1 Minor fix in Unimarc 100/MARC 008 field
         CAUTION: Translation is still partial and not thoroughly tested.
                  May include obsolete fields/subfields or miss important new fields/subfields. Most translations based on [1], which is dated from 2001!
  - v3.0 (by Iordanis Kostelidis)
         Mix v2.1 with the version from marcEdit's creator
         TODO:
                  Work needs to be done in the xsl for indicator handling/translation.
                  Translation of more fields/subfields must be taken into account.
                  After proper indicator handling, review all current translations.


-->
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="marc:collection">
                <collection xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd">
                    <xsl:for-each select="marc:collection/marc:record">
                        <record>
                            <xsl:call-template name="record"/>
                        </record>
                    </xsl:for-each>
                </collection>
            </xsl:when>
            <xsl:otherwise>
                <collection xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                            xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd">
                    <xsl:for-each select="marc:collection/marc:record">
                        <record>
                            <xsl:call-template name="record"/>
                        </record>
                    </xsl:for-each>
                </collection>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="record">
        <xsl:if test="@type">
            <xsl:attribute name="type">
                <xsl:value-of select="@type"/>
            </xsl:attribute>
        </xsl:if>
        <xsl:call-template name="transform-leader"/>
        <xsl:call-template name="copy-control">
            <xsl:with-param name="tag">001</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="copy-control">
            <xsl:with-param name="tag">005</xsl:with-param>
        </xsl:call-template>
        <xsl:call-template name="transform-100"/>

        <!--  010->020  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">010</xsl:with-param>
            <xsl:with-param name="dstTag">020</xsl:with-param>
            <xsl:with-param name="srcCodes">abdz</xsl:with-param>
            <xsl:with-param name="dstCodes">abcz</xsl:with-param>
        </xsl:call-template>
        <!--  011->022  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">011</xsl:with-param>
            <xsl:with-param name="dstTag">022</xsl:with-param>
            <xsl:with-param name="srcCodes">az</xsl:with-param>
            <xsl:with-param name="dstCodes">ay</xsl:with-param>
        </xsl:call-template>
        <!--  200->245  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">200</xsl:with-param>
            <xsl:with-param name="dstTag">245</xsl:with-param>
            <xsl:with-param name="srcCodes">aefb</xsl:with-param>
            <xsl:with-param name="dstCodes">abch</xsl:with-param>
        </xsl:call-template>
        <!--  205->250  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">205</xsl:with-param>
            <xsl:with-param name="dstTag">250</xsl:with-param>
            <xsl:with-param name="srcCodes">aefb</xsl:with-param>
            <xsl:with-param name="dstCodes">abch</xsl:with-param>
        </xsl:call-template>
        <!--  210->260  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">210</xsl:with-param>
            <xsl:with-param name="dstTag">260</xsl:with-param>
            <xsl:with-param name="srcCodes">acdegh</xsl:with-param>
            <xsl:with-param name="dstCodes">abcefg</xsl:with-param>
        </xsl:call-template>
        <!--  215->300  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">215</xsl:with-param>
            <xsl:with-param name="dstTag">300</xsl:with-param>
            <xsl:with-param name="srcCodes">acde</xsl:with-param>
            <xsl:with-param name="dstCodes">abce</xsl:with-param>
        </xsl:call-template>
        <!--  225->490  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">225</xsl:with-param>
            <xsl:with-param name="dstTag">490</xsl:with-param>
            <xsl:with-param name="srcCodes">avx</xsl:with-param>
            <xsl:with-param name="dstCodes">avx</xsl:with-param>
        </xsl:call-template>
        <!--  300->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">300</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
        </xsl:call-template>
        <!--  301->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">301</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  302->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">302</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  303->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">303</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  304->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">304</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  305->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">305</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  306->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">306</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  307->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">307</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  308->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">308</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  310->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">310</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  311->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">311</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  312->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">312</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  313->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">313</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  314->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">314</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  315->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">315</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  320->504  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">320</xsl:with-param>
            <xsl:with-param name="dstTag">504</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  321->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">321</xsl:with-param>
            <xsl:with-param name="dstTag">500</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  322->500  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">322</xsl:with-param>
            <xsl:with-param name="dstTag">508</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  325->533  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">325</xsl:with-param>
            <xsl:with-param name="dstTag">533</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">n</xsl:with-param>
        </xsl:call-template>
        <!--  327->505  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">327</xsl:with-param>
            <xsl:with-param name="dstTag">505</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  328->502  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">328</xsl:with-param>
            <xsl:with-param name="dstTag">502</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  330->520  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">330</xsl:with-param>
            <xsl:with-param name="dstTag">520</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  333->521  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">333</xsl:with-param>
            <xsl:with-param name="dstTag">521</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--
         335->856 (Needs checking. New field, not included in LOC transformation doc)
        -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">335</xsl:with-param>
            <xsl:with-param name="dstTag">856</xsl:with-param>
            <xsl:with-param name="srcCodes">gu8</xsl:with-param>
            <xsl:with-param name="dstCodes">wu3</xsl:with-param>
        </xsl:call-template>
        <!--
         337->856 (Needs checking. New field, not included in LOC transformation doc)
        -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">337</xsl:with-param>
            <xsl:with-param name="dstTag">856</xsl:with-param>
            <xsl:with-param name="srcCodes">au</xsl:with-param>
            <xsl:with-param name="dstCodes">zu</xsl:with-param>
        </xsl:call-template>
        <!--  345->037  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">345</xsl:with-param>
            <xsl:with-param name="dstTag">037</xsl:with-param>
            <xsl:with-param name="srcCodes">abcd</xsl:with-param>
            <xsl:with-param name="dstCodes">bafc</xsl:with-param>
        </xsl:call-template>
        <!--  410->760  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">410</xsl:with-param>
            <xsl:with-param name="dstTag">760</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  411->762  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">411</xsl:with-param>
            <xsl:with-param name="dstTag">762</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  421->770  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">421</xsl:with-param>
            <xsl:with-param name="dstTag">770</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  422->772  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">422</xsl:with-param>
            <xsl:with-param name="dstTag">772</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  423->777  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">423</xsl:with-param>
            <xsl:with-param name="dstTag">777</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  430->780  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">430</xsl:with-param>
            <xsl:with-param name="dstTag">780</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  431->780  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">431</xsl:with-param>
            <xsl:with-param name="dstTag">780</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  432->780  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">432</xsl:with-param>
            <xsl:with-param name="dstTag">780</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  433->780  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">433</xsl:with-param>
            <xsl:with-param name="dstTag">780</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  452->776  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">452</xsl:with-param>
            <xsl:with-param name="dstTag">776</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  453->767  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">453</xsl:with-param>
            <xsl:with-param name="dstTag">767</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  454->765  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">454</xsl:with-param>
            <xsl:with-param name="dstTag">765</xsl:with-param>
            <xsl:with-param name="srcCodes">3tvxy</xsl:with-param>
            <xsl:with-param name="dstCodes">wtgxz</xsl:with-param>
        </xsl:call-template>
        <!--  531->210  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">531</xsl:with-param>
            <xsl:with-param name="dstTag">210</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  540->246  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">540</xsl:with-param>
            <xsl:with-param name="dstTag">246</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  541->242  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">541</xsl:with-param>
            <xsl:with-param name="dstTag">242</xsl:with-param>
            <xsl:with-param name="srcCodes">aehiz</xsl:with-param>
            <xsl:with-param name="dstCodes">abnpy</xsl:with-param>
        </xsl:call-template>
        <!--  545->773  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">545</xsl:with-param>
            <xsl:with-param name="dstTag">773</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">t</xsl:with-param>
        </xsl:call-template>
        <!--  600->600  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">600</xsl:with-param>
            <xsl:with-param name="dstTag">600</xsl:with-param>
            <xsl:with-param name="srcCodes">acdftxyz</xsl:with-param>
            <xsl:with-param name="dstCodes">acbdtxzy</xsl:with-param>
        </xsl:call-template>
        <!--  602->600  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">602</xsl:with-param>
            <xsl:with-param name="dstTag">600</xsl:with-param>
            <xsl:with-param name="srcCodes">atxyz</xsl:with-param>
            <xsl:with-param name="dstCodes">atxzy</xsl:with-param>
        </xsl:call-template>
        <!--  606->650  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">606</xsl:with-param>
            <xsl:with-param name="dstTag">650</xsl:with-param>
            <xsl:with-param name="srcCodes">axyz</xsl:with-param>
            <xsl:with-param name="dstCodes">axzy</xsl:with-param>
        </xsl:call-template>
        <!--  607->651  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">607</xsl:with-param>
            <xsl:with-param name="dstTag">651</xsl:with-param>
            <xsl:with-param name="srcCodes">axyz</xsl:with-param>
            <xsl:with-param name="dstCodes">axzy</xsl:with-param>
        </xsl:call-template>
        <!--  610->653  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">610</xsl:with-param>
            <xsl:with-param name="dstTag">653</xsl:with-param>
            <xsl:with-param name="srcCodes">a</xsl:with-param>
            <xsl:with-param name="dstCodes">a</xsl:with-param>
        </xsl:call-template>
        <!--  615->650  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">615</xsl:with-param>
            <xsl:with-param name="dstTag">650</xsl:with-param>
            <xsl:with-param name="srcCodes">ax</xsl:with-param>
            <xsl:with-param name="dstCodes">ax</xsl:with-param>
        </xsl:call-template>
        <!--  615->072  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">615</xsl:with-param>
            <xsl:with-param name="dstTag">072</xsl:with-param>
            <xsl:with-param name="srcCodes">nm</xsl:with-param>
            <xsl:with-param name="dstCodes">ax</xsl:with-param>
        </xsl:call-template>
        <!--  620->752  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">620</xsl:with-param>
            <xsl:with-param name="dstTag">752</xsl:with-param>
            <xsl:with-param name="srcCodes">abcd</xsl:with-param>
            <xsl:with-param name="dstCodes">abcd</xsl:with-param>
        </xsl:call-template>
        <!--  626->753  -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">626</xsl:with-param>
            <xsl:with-param name="dstTag">753</xsl:with-param>
            <xsl:with-param name="srcCodes">abc</xsl:with-param>
            <xsl:with-param name="dstCodes">abc</xsl:with-param>
        </xsl:call-template>
        <!--  700->100  -->
        <xsl:call-template name="transform-personal-name">
            <xsl:with-param name="srcTag">700</xsl:with-param>
            <xsl:with-param name="dstTag">100</xsl:with-param>
        </xsl:call-template>
        <!--  701->700  -->
        <xsl:call-template name="transform-personal-name">
            <xsl:with-param name="srcTag">701</xsl:with-param>
            <xsl:with-param name="dstTag">700</xsl:with-param>
        </xsl:call-template>
        <!--  702->700  -->
        <xsl:call-template name="transform-personal-name">
            <xsl:with-param name="srcTag">702</xsl:with-param>
            <xsl:with-param name="dstTag">700</xsl:with-param>
        </xsl:call-template>
        <!--
         856->856 (Needs checking. New field, not included in LOC transformation doc)
        -->
        <xsl:call-template name="transform-datafield">
            <xsl:with-param name="srcTag">856</xsl:with-param>
            <xsl:with-param name="dstTag">856</xsl:with-param>
            <xsl:with-param name="srcCodes">2abcdfhijklmnopqrstuvwxz</xsl:with-param>
            <xsl:with-param name="dstCodes">yabcdfhijklmnopqrstuvwxz</xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="selects">
        <xsl:param name="i"/>
        <xsl:param name="count"/>

        <xsl:if test="$i &lt;= $count">
            <xsl:call-template name="transform-datafield">
                <xsl:with-param name="srcTag">
                    <xsl:value-of select="$i"/>
                </xsl:with-param>
                <xsl:with-param name="dstTag">
                    <xsl:value-of select="$i"/>
                </xsl:with-param>
                <xsl:with-param name="srcCodes">
                    <xsl:value-of select="$all-codes"/>
                </xsl:with-param>
                <xsl:with-param name="dstCodes">
                    <xsl:value-of select="$all-codes"/>
                </xsl:with-param>
            </xsl:call-template>
        </xsl:if>

        <!--begin_: RepeatTheLoopUntilFinished-->
        <xsl:if test="$i &lt; $count">
            <xsl:call-template name="selects">
                <xsl:with-param name="i">
                    <xsl:value-of select="$i + 1"/>
                </xsl:with-param>
                <xsl:with-param name="count">
                    <xsl:value-of select="$count"/>
                </xsl:with-param>
            </xsl:call-template>
        </xsl:if>
    </xsl:template>


    <xsl:template name="transform-leader">
        <xsl:variable name="leader" select="marc:leader"/>
        <xsl:variable name="leader05" select="translate(substring($leader,06,1), 'o', 'c')"/>
        <xsl:variable name="leader06" select="translate(substring($leader,07,1), 'hmn', 'aor')"/>
        <xsl:variable name="leader07" select="substring($leader,08,1)"/>
        <xsl:variable name="leader08-16" select="'  22     '"/>
        <xsl:variable name="leader17" select="translate(substring($leader,18,1), '23', '87')"/>
        <xsl:variable name="leader18" select="translate(substring($leader,19,1), ' n', 'i ')"/>
        <xsl:variable name="leader19-23" select="' 4500'"/>
        <leader>
            <xsl:value-of
                    select="concat('     ', $leader05, $leader06, $leader07, $leader08-16, $leader17, $leader18, $leader19-23)"/>
        </leader>
    </xsl:template>
    <xsl:template name="copy-control">
        <xsl:param name="tag"/>
        <xsl:for-each select="marc:controlfield[@tag=$tag]">
            <controlfield tag="{$tag}">
                <xsl:value-of select="text()"/>
            </controlfield>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="transform-100">
        <xsl:variable name="source" select="marc:datafield[@tag='100']/marc:subfield[@code='a']"/>
        <xsl:variable name="dest00-05" select="substring($source,03,6)"/>
        <xsl:variable name="dest06" select="translate(substring($source,09,1), 'abcdefghij', 'cdusrqmtpe')"/>
        <xsl:variable name="dest07-14" select="substring($source,10,8)"/>
        <xsl:variable name="dest15-21" select="'       '"/>
        <xsl:variable name="dest22" select="translate(substring($source,18,1), 'bcadekmu', 'abjcdeg ')"/>
        <xsl:variable name="dest23-27" select="'     '"/>
        <xsl:variable name="dest28" select="translate(substring($source,21,1), 'abcdefghy', 'fsllcizo ')"/>
        <xsl:variable name="dest29-32" select="'    '"/>
        <xsl:variable name="dest33" select="substring($source,35,1)"/>
        <xsl:variable name="dest34-37" select="'    '"/>
        <xsl:variable name="dest38" select="translate(substring($source,22,1), '01', ' o')"/>
        <xsl:variable name="dest39-40" select="'  '"/>
        <controlfield tag="008">
            <xsl:value-of
                    select="concat($dest00-05, $dest06, $dest07-14, $dest15-21, $dest22, $dest23-27, $dest28, $dest29-32, $dest33, $dest34-37, $dest38, $dest39-40)"/>
        </controlfield>
    </xsl:template>
    <xsl:template name="transform-datafield">
        <xsl:param name="srcTag"/>
        <xsl:param name="dstTag" select="@srcTag"/>
        <xsl:param name="srcCodes" select="$all-codes"/>
        <xsl:param name="dstCodes" select="$srcCodes"/>
        <xsl:if test="marc:datafield[@tag=$srcTag]/marc:subfield[contains($srcCodes, @code)]">
            <xsl:for-each select="marc:datafield[@tag=$srcTag]">
                <datafield tag="{$dstTag}">
                    <xsl:call-template name="copy-indicators"/>
                    <xsl:call-template name="transform-subfields">
                        <xsl:with-param name="srcCodes" select="$srcCodes"/>
                        <xsl:with-param name="dstCodes" select="$dstCodes"/>
                    </xsl:call-template>
                </datafield>
            </xsl:for-each>
        </xsl:if>
    </xsl:template>
    <xsl:template name="transform-personal-name">
        <xsl:param name="srcTag"/>
        <xsl:param name="dstTag"/>

        <xsl:for-each select="marc:datafield[@tag=$srcTag]">
            <datafield tag="{$dstTag}" ind1="{@ind2}" ind2="">
                <xsl:call-template name="transform-subfields">
                    <xsl:with-param name="srcCodes" select="'acdfgp4'"/>
                    <xsl:with-param name="dstCodes" select="'acbdqu4'"/>
                </xsl:call-template>
            </datafield>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="copy-indicators">
        <xsl:attribute name="ind1">
            <xsl:value-of select="translate(@ind1, '#', '')"/>
        </xsl:attribute>
        <xsl:attribute name="ind2">
            <xsl:value-of select="translate(@ind2, '#', '')"/>
        </xsl:attribute>
    </xsl:template>
    <xsl:template name="transform-subfields">
        <xsl:param name="srcCodes" select="$all-codes"/>
        <xsl:param name="dstCodes" select="$srcCodes"/>
        <xsl:for-each select="marc:subfield[contains($srcCodes, @code)]">
            <subfield code="{translate(@code, $srcCodes, $dstCodes)}">
                <xsl:value-of select="text()"/>
            </subfield>
        </xsl:for-each>
    </xsl:template>

    <xsl:variable name="all-codes">abcdefghijklmnopqrstuvwxyz123456789</xsl:variable>
</xsl:stylesheet>