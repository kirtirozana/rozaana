<?php

class Meetanshi_Restrictzip_Model_Import extends Mage_Core_Model_Abstract
{
    public function uploadAndImport()
    {

        if (empty($_FILES['groups']['tmp_name']['restrictzip']['fields']['import']['value'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['restrictzip']['fields']['import']['value'];

        $io = new Varien_Io_File();
        $info = pathinfo($csvFile);
        $io->open(array('path' => $info['dirname']));
        $io->streamOpen($info['basename'], 'r');

        $headers = $io->streamReadCsv();
        if ($headers === false || count($headers) < 1) {
            $io->streamClose();
            Mage::throwException(Mage::helper('restrictzip')->__('Invalid CSV File'));
        }

        try {
            $count = 0;
            while (false !== ($csvLine = $io->streamReadCsv())) {
                if (empty($csvLine) || $csvLine[0] == '')
                    continue;

                $zipcodeModel = Mage::getModel('restrictzip/restrictzip');
                $zipcodeModel->load($csvLine[0], 'zip_code');
                $zipcodeModel->setZipCode($csvLine[0])
                    ->setEstimateDelTime($csvLine[1])
                    ->save();
                $count++;
            }

            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('restrictzip')->__('Successfully imported ' . $count . ' ZIP Codes')
            );
        } catch (Mage_Core_Exception $e) {
            Mage::throwException($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(
                Mage::helper('restrictzip')->__(
                    '
            An error occurred while importing COD zip codes.'
                )
            );
        }

        if ($this->_importErrors) {
            $error = Mage::helper('restrictzip')->__(
                '
            File has not been imported. See the following list of errors: %s',
                implode(" \n", $this->_importErrors)
            );
            Mage::throwException($error);
        }

        return $this;
    }
}
