<?php


class IperStore
{
    private $storeId;
    private $pdrId;
    private $storeURL;

    function __construct(int $storeId, int $pdrId)
    {
        $this->storeId  = $storeId;
        $this->pdrId    = $pdrId;
        $this->storeURL = "https://iperdrive.iper.it/spesa-online/SlotDisplayView?sync=1&langId=-4&storeId=$storeId&pdrId=$pdrId";
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->storeId;
    }

    /**
     * @param int $storeId
     */
    public function setStoreId(int $storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @return int
     */
    public function getPdrId(): int
    {
        return $this->pdrId;
    }

    /**
     * @param int $pdrId
     */
    public function setPdrId(int $pdrId)
    {
        $this->pdrId = $pdrId;
    }

    /**
     * @return string
     */
    public function getStoreURL(): string
    {
        return $this->storeURL;
    }

    /**
     * @param string $storeURL
     */
    public function setStoreURL(string $storeURL)
    {
        $this->storeURL = $storeURL;
    }

    public function getAvailabilities() : array
    {
        $jsonData = $this->getJSONData();
        if ($jsonData){
            //find availabilities
            $availabilities = [];
            foreach ($jsonData->orario as $day) {
                //should be 3 days
                $availabilities[$day->dayDate] = [];
                foreach ($day->slots as $slot) {
                    //check if active is different from -1 and 0 (idk why they use -1)
                    if ($slot->active != -1 && $slot->active != 0) {
                        array_push($availabilities[$day->dayDate], $slot->title);
                    }
                }
            }

            return $availabilities;
        }
    }

    private function getJSONData()
    {
        //retrieve selected IPER Drive page
        $webContent = file_get_contents($this->storeURL);

        //I've found that the slots information are stored in page as JSON contained
        // in StoreLocatorJS.initOrari() JS function
        if (preg_match('/StoreLocatorJS.initOrari(.*?);/', $webContent, $jsonAvailabilities) === 1) {
            //Turn string into object trimming round brackets
            $jsonData = json_decode(trim($jsonAvailabilities[1], "(\)"));
            if (isset($jsonData->orario)) {
                return json_decode(trim($jsonAvailabilities[1], "(\)"));
            }
        }

        return false;
    }
}