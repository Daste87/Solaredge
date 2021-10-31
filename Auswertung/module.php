<?php

declare(strict_types=1);
	class Auswertung extends IPSModule
	{
		public function Create()
		{
			//Never delete this line!
			parent::Create();

            $this->RegisterVariableFloat("Actual", $this->Translate("Actual"),"",0);
            $this->RegisterVariableFloat("Today", $this->Translate("Today"),"",1);
            $this->RegisterVariableFloat("Month", $this->Translate("Month"),"",2);
            $this->RegisterVariableFloat("Year", $this->Translate("Year"),"",3);
            $this->RegisterVariableFloat("Total", $this->Translate("Total"),"",4); 

            $this->RegisterPropertyInteger("Aktualisierungsinvertall", 900);
            $this->RegisterPropertyString("ApiKey", "");
            $this->RegisterPropertyString("Id", "");

            //Create the timer for the updateintervall
            $this->RegisterTimer("Update", 900, 'A_UpdateAuswertung(' . $this->InstanceID .');');
            
		}

		public function Destroy()
		{
			//Never delete this line!
			parent::Destroy();
		}

		public function ApplyChanges()
		{
			//Never delete this line!
			parent::ApplyChanges();

            $this->SetTimerInterval("Update", $this->ReadPropertyInteger("Aktualisierungsinvertall") * 1000);
            $this->UpdateAuswertung();

		}


		public function UpdateAuswertung () {

            $id=$this->ReadPropertyString("Id");
            $apikey=$this->ReadPropertyString("ApiKey");
                        if ($id != "")
 
        
            
            {   
                $content = Sys_GetURLContent("https://monitoringapi.solaredge.com/site/$id/overview?api_key=$apikey"); 
                $json=json_decode($content);

                $cache = $json->overview->currentPower->power / 1000;
                $this->SetValue("Actual", $cache);

                # Generated day
                $cache = $json->overview->lastDayData->energy / 1000;
                $this->SetValue("Today", $cache);

                # Generated month
                $cache = $json->overview->lastMonthData->energy / 1000;
                $this->SetValue("Month", $cache);

                # Generated year
                $cache = $json->overview->lastYearData->energy / 1000;
                $this->SetValue("Year", $cache);

                # Generated all
                $cache = $json->overview->lifeTimeData->energy / 1000;
                $this->SetValue("Total", $cache);
            }

        }

	}