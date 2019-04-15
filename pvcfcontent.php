<?php
/**
 * @version     $Id: pvcfcontent.php
 * @package     PVotes
 * @subpackage  Content
 * @copyright   Copyright (C) 2015 Philadelphia Elections Commission
 * @license     GNU/GPL, see LICENSE.php
 * @author      Matthew Murphy <matthew.e.murphy@phila.gov>
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Example Content Plugin
 *
 * @package     Joomla
 * @subpackage  Content
 * @since       1.5
 */
class plgContentPvcfcontent extends JPlugin
{

    /**
     * Constructor
     *
     * @param object $subject The object to observe
     * @param object $params  The object that holds the plugin parameters
     * @since 1.5
     */
    public function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);
    }

    /**
     * Default event
     *
     * Isolate the content and call actual processor
     *
     * @param   object      The article object.  Note $article->text is also available
     * @param   object      The article params
     * @param   int         The 'page' number
     */
    public function onPrepareContent(&$article, &$params, $limitstart)
    {
        global $mainframe;
        if (is_object($article)) {
            return $this->getPvcfcontentDisplay($article->text);
        }
        return $this->getPvcfcontentDisplay($article);
    }

    /**
     * Example after display title method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param   object   $article   The article object.  Note $article->text is also available
     * @param   object   $params   The article params
     * @param   int      $limitstart   The 'page' number
     * @return  string
     */
    public function onAfterDisplayTitle(&$article, &$params, $limitstart)
    {
        global $mainframe;

        return '';
    }

    /**
     * Example before display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param   object   $article   The article object.  Note $article->text is also available
     * @param   object   $params   The article params
     * @param   int      $limitstart   The 'page' number
     * @return  string
     */
    public function onBeforeDisplayContent(&$article, &$params, $limitstart)
    {
        global $mainframe;

        return '';
    }

    /**
     * Example after display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param   object   $article   The article object.  Note $article->text is also available
     * @param   object   $params   The article params
     * @param   int      $limitstart   The 'page' number
     * @return  string
     */
    public function onAfterDisplayContent(&$article, &$params, $limitstart)
    {
        global $mainframe;

        return '';
    }

    /**
     * Example before save content method
     *
     * Method is called right before content is saved into the database.
     * Article object is passed by reference, so any changes will be saved!
     * NOTE:  Returning false will abort the save with an error.
     *  You can set the error by calling $article->setError($message)
     *
     * @param   object   $article   A JTableContent object
     * @param   bool     $isNew   If the content is just about to be created
     * @return  bool        If false, abort the save
     */
    public function onBeforeContentSave(&$article, $isNew)
    {
        global $mainframe;

        return true;
    }

    /**
     * Example after save content method
     * Article is passed by reference, but after the save, so no changes will be saved.
     * Method is called right after the content is saved
     *
     *
     * @param   object   $article   A JTableContent object
     * @param   bool     $isNew   If the content is just about to be created
     * @return  void
     */
    public function onAfterContentSave(&$article, $isNew)
    {
        global $mainframe;

        return true;
    }

    /**
     * Check for a Pvcfcontent block,
     * skip <script> blocks, and
     * call getPvcfcontentStrings() as appropriate.
     *
     * @param   string   $text  content
     * @return  bool
     */
    public function getPvcfcontentDisplay(&$text)
    {
        // Quick, cheap chance to back out.
        if (JString::strpos($text, 'PVCFCONTENT') === false) {
            return true;
        }

        $text = explode('<script', $text);
        foreach ($text as $i => $str) {
            if ($i == 0) {
                $this->getPvcfcontentStrings($text[$i]);
            } else {
                $str_split = explode('</script>', $str);
                foreach ($str_split as $j => $str_split_part) {
                    if (($j % 2) == 1) {
                        $this->getPvcfcontentStrings($str_split[$i]);
                    }
                }
                $text[$i] = implode('</script>', $str_split);
            }
        }
        $text = implode('<script', $text);

        return true;
    }

    /**
     * Find Pvcfcontent blocks,
     * get display per block.
     *
     * @param   string   $text  content
     * @return  bool
     */
    public function getPvcfcontentStrings(&$text)
    {
        // Quick, cheap chance to back out.
        if (JString::strpos($text, 'PVCFCONTENT') === false) {
            return true;
        }

        $search = "(\[\[PVCFCONTENT|.*\]\])";

        while (preg_match($search, $text, $regs, PREG_OFFSET_CAPTURE)) {
            $matches = explode('|', trim(trim($regs[0][0], '[]'), '[]'));
            jimport('kint.kint');
            d($matches);
            $mock = array(1=>
                array(
                    'entity'=>'Maria Quiñones-Sánchez 7<sup>th</sup> District', 
                    'url'=>'https://pdfgen.phila.gov/pdf/5c3e30919006b/2002?aid=Y1B0L0haQ29MdDFxQ1F1WnVrdUNjQmNhelFsWkFoTGNRQ0dwSXM1S1Vmbz0=',
                    'committee'=>1,
                    'amended'=>'',
                    'termination'=>0,
                    'reporttype'=>'District Council',
                ),
                array(
                    'entity'=>'Some Guy', 
                    'url'=>'https://pdfgen.phila.gov/pdf/5c3e30919006b/2002?aid=Y1B0L0haQ29MdDFxQ1F1WnVrdUNjQmNhelFsWkFoTGNRQ0dwSXM1S1Vmbz0=',
                    'committee'=>1,
                    'amended'=>1,
                    'termination'=>1,
                    'reporttype'=>'Mayor',
                ),
            );

            if ($matches[3]) {
                // GET SOME DATA

                // shape data
                $text = JString::str_ireplace($regs[0][0], "" . $this->getReportypeDisplay($mock, 'online') . "", $text);
                return true;
            }

            if ($matches[2]) {
                // GET SOME DATA

                // shape data
                $text = JString::str_ireplace($regs[0][0], "" . $this->getFullDisplay($mock, 'paper') . "", $text);
                return true;
            }

            // Woops.  Didn't find enough to get results.
            $text = JString::str_ireplace($regs[0][0], "<div class=\"error\">Usage: [[PVCFCONTENT|YEAR|CYCLE|OFFICE/ENTITYTYPE(OPTIONAL)]].</div>", $text);
        }
        return true;
    }

    /**
     * Get HTML content,
     *
     * @param   $file_Path
     * @return  string
     */
    public function getHTMLContent($file_path)
    {
        return "<h2>HTML CONTENT</h2>";
    }

    public function getFullDisplay($rows, $source) {
        if ($source == 'online') {
            $content = '<h4>Filed Online (<a href="https://apps.phila.gov/campaign-finance/search/contributions" target="_blank">Search here</a>):</h4>';
        } else if ($source == 'paper') {
            $content = '<h4>Paper filing:</h4>';
        }
        $old_reporttype = '';
        foreach ($rows as $key => $row) {
            // we're assuming the data is sorted by reporttype here
            if ($row['reporttype'] == $old_reporttype) {
                // no header
            } else {
                $content.="<h5>" . $row['reporttype'] . "</h5>";
            }
            $content .= $this->getReportLine($row);
        }
        return $content;
    }

    public function getReportypeDisplay($rows, $source) {
        if ($source == 'online') {
            $content = '<h4>Filed Online (<a href="https://apps.phila.gov/campaign-finance/search/contributions" target="_blank">Search here</a>):</h4>';
        } else if ($source == 'paper') {
            $content = '<h4>Paper filing:</h4>';
        }
        foreach ($rows as $key => $row) {
            $content .= $this->getReportLine($row);
        }
        return $content;
    }

    public function getReportLine($row) {
        // build 'flags' content
        if ($row['committee'] || $row['amended'] || $row['termination']) {
            $flags=' (';
            if ($row['committee']) {
                $flagged=1;
                $flags.='committee';
            }
            if ($row['amended']) {
                if ($flagged) {
                    $flags.=', ';
                }
                $flags.='amended';
                $flagged=1;
            }
            if ($row['termination']) {
                if ($flagged) {
                    $flags.=', ';
                }
                $flags.='termination';
            }
            $flags.=')';
        }

        return '<p><a href="' . $row['url'] . '" target="_blank">' . $row['entity'] . '</a>' . $flags . '</p>';
    }

}
