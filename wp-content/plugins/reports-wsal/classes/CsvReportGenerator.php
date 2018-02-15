<?php if(!class_exists('WSAL_Rep_Plugin')){ exit('You are not allowed to view this page.'); }
/**
 * Class WSAL_Rep_CsvReportGenerator
 * Provides utility methods to generate a csv report
 */
class WSAL_Rep_CsvReportGenerator
{
    protected $datetime_format = null;

    public function __construct($datetime_format)
    {
        $this->datetime_format = $datetime_format;
    }
    function Generate(array $data, $uploadsDirPath, $delim = ',')
    {
        if (empty($data)) {
            return 0;
        }
        // Split data by blog so we can display an organized report
        $tempData = array();
        foreach ($data as $k => $entry) {
            $blogName = $entry['blog_name'];
            if (!isset($tempData[$blogName])) {
                $tempData[$blogName] = array();

            }
            array_push($tempData[$blogName], $entry);
        }

        if (empty($tempData)) {
            return 0;
        }

        // Check directory once more
        if (! is_dir($uploadsDirPath) || !is_readable($uploadsDirPath) || !is_writable($uploadsDirPath)) {
            return 1;
        }

        $fn = 'wsal_report_'.WSAL_Rep_Util_S::GenerateRandomString().'.csv';
        $fp = $uploadsDirPath.$fn;

        $file = fopen($fp, 'w');

        // Add columns
        $columns = array(array(
            'Blog Name',
            'Code',
            'Type',
            'Date',
            'Username',
            'Role',
            'Source IP',
            'Messsage'));
        $out = '';
        foreach ($columns as $row) {
            $quoted_data = array_map(array($this, 'quote'), $row);
            $out .= sprintf("%s\n", implode($delim, $quoted_data));
        }
        fwrite($file, $out);

        foreach ($tempData as $blogName => $entry) {
            // Add rows
            foreach ($entry as $k => $alert) {
                // Date Format compatible with Excel
                $aDate = explode('.', $alert['date']);
                $date = DateTime::createFromFormat($this->datetime_format, $aDate[0]);
                $newDateString = $date->format("d/m/Y h:i:s A");
                $values = array(array(
                    $alert['blog_name'],
                    $alert['alert_id'],
                    $alert['code'],
                    $newDateString,
                    $alert['user_name'],
                    $alert['role'],
                    $alert['user_ip'],
                    $alert['message']
                ));
                $out = '';
                foreach ($values as $row) {
                    $quoted_data = array_map(array($this, 'quote'), $row);
                    $out .= sprintf("%s\n", implode($delim, $quoted_data));
                }
                fwrite($file, $out);
            }
        }
        fclose($file);
        return $fn;
    }

    /**
     * Utility method to quote the given item
     * @internal
     * @param mixed $data
     * @return string
     */
    final public function quote($data)
    {
        $data = preg_replace('/"(.+)"/', '""$1""', $data);
        return sprintf('"%s"', $data);
    }
}
