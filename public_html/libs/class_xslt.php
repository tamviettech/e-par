<?php

//LienND: Support PHP5

if (PHP_VERSION >= 5) {

    // Emulate the old xslt library functions
    function xslt_create() {
        return new XsltProcessor();
    }

    function xslt_process($xsltproc, $xml_arg, $xsl_arg, $xslcontainer = null, $args = null, $params = null) {
        // Start with preparing the arguments
        $xml_arg = str_replace('arg:', '', $xml_arg);
        $xsl_arg = str_replace('arg:', '', $xsl_arg);

        // Create instances of the DomDocument class
        $xml = new DomDocument;
        $xsl = new DomDocument;

        // Load the xml document and the xsl template
        $xml->loadXML($args[$xml_arg]);
        $xsl->loadXML($args[$xsl_arg]);

        // Load the xsl template
        $xsltproc->importStyleSheet($xsl);

        // Set parameters when defined
        if ($params) {
            foreach ($params as $param => $value) {
                $xsltproc->setParameter("", $param, $value);
            }
        }

        // Start the transformation
        $processed = $xsltproc->transformToXML($xml);

        // Put the result in a file when specified
        if ($xslcontainer) {
            return @file_put_contents($xslcontainer, $processed);
        } else {
            return $processed;
        }
    }

    function xslt_free($xsltproc) {
        unset($xsltproc);
    }

}


// ##################################################################################
// Title                     : class_xslt.php
// Version                   : 1.1
// Author                    : Luis Argerich (lrargerich@yahoo.com)
// Last modification date    : 03-11-2002
// Description               : An abstraction class for the XSLT extension
//                             this one uses the Sablotron processor but we
//                             may release classes based on other processors
//                             later.
// CHECK FOR DOUBLE DEFINITION HERE
if (defined("_class_xslt_is_included")) {
    // do nothing since the class is already included  
} else {
    define("_class_xslt_is_included", 1);

    class Xslt {

        var $xsl, $xml, $output, $error;

        /* Constructor */

        function xslt() {
            $this->processor = xslt_create();
        }

        /* Destructor */

        function destroy() {
            xslt_free($this->processor);
        }

        /* output methods */

        function setOutput($string) {
            $this->output = $string;
        }

        function getOutput() {
            return $this->output;
        }

        /* set methods */

        function setXmlString($xml) {
            $this->xml = $xml;
            return true;
        }

        function setXslString($xsl) {
            $this->xsl = $xsl;
            return true;
        }

        function setXml($uri) {
            if ($doc = new docReader($uri)) {
                $this->xml = $doc->getString();
                return true;
            } else {
                $this->setError("Could not open $xml");
                return false;
            }
        }

        function setXsl($uri) {
            if ($doc = new docReader($uri)) {
                $this->xsl = $doc->getString();
                return true;
            } else {
                $this->setError("Could not open $uri");
                return false;
            }
        }

        /* transform method */

        function transform() {
            $arguments = array(
                '/_xml' => $this->xml,
                '/_xsl' => $this->xsl
            );
            $ret = xslt_process($this->processor, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments);
            if (!$ret) {
                $this->setError(xslt_error($this->processor));
                return false;
            } else {
                $this->setOutput($ret);
                return true;
            }
        }

        /* Error Handling */

        function setError($string) {
            $this->error = $string;
        }

        function getError() {
            return $this->error;
        }

        public function setParameter($arrParam) {
            $this->processor->registerPHPFunctions();
            $this->processor->setParameter(NULL,$arrParam);
        }

    }

    /* docReader -- read a file or URL as a string */
    /* test */
    /*
      $docUri = new docReader('http://www.someurl.com/doc.html');
      echo $docUri->getString();
     */

    class docReader {

        var $string; // public string representation of file 
        var $type; // private URI type: 'file','url' 
        var $bignum = 1000000;
        var $uri;

        /* public constructor */

        function docReader($uri) { // returns integer      $this->setUri($uri); 
            $this->uri = $uri;
            $this->setType();
            $fp = fopen($this->getUri(), "r");
            if ($fp) { // get length 
                if ($this->getType() == 'file') {
                    $length = filesize($this->getUri());
                } else {
                    $length = $this->bignum;
                }
                $this->setString(fread($fp, $length));
                return 1;
            } else {
                return 0;
            }
        }

        /* determine if a URI is a filename or URL */

        function isFile($uri) { // returns boolean
            if (strstr($uri, 'http://') == $uri) {
                return false;
            } else {
                return true;
            }
        }

        /* set and get methods */

        function setUri($string) {
            $this->uri = $string;
        }

        function getUri() {
            return $this->uri;
        }

        function setString($string) {
            $this->string = $string;
        }

        function getString() {
            return $this->string;
        }

        function setType() {
            if ($this->isFile($this->uri)) {
                $this->type = 'file';
            } else {
                $this->type = 'url';
            }
        }

        function getType() {
            return $this->type;
        }

    }

}
?>
