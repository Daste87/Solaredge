<?
    // Klassendefinition
    class Auswertung extends IPSModule {
 
        // Überschreibt die interne IPS_Create($id) Funktion
        public function Create() {
            // Diese Zeile nicht löschen.
            parent::Create();

            $this->RegisterVariableFloat("Actual", $this->Translate("Actual"),"",0);
            $this->RegisterVariableFloat("Today", $this->Translate("Today"),"",1);
            $this->RegisterVariableFloat("Month", $this->Translate("Month"),"",2);
            $this->RegisterVariableFloat("Year", $this->Translate("Year"),"",3);
            $this->RegisterVariableFloat("Total", $this->Translate("Total"),"",4); 

            $this->RegisterPropertyInteger("Aktualisierungsinvertall", 9100000);
            $this->RegisterPropertyString("ApiKey", "");
            $this->RegisterPropertyString("Id", "");

            $this->RegisterTimer("Update", 9000000, 'A_UpdateAuswertung(' . $this->InstanceID . ', "Id","ApiKey");');
            
        }
        
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        // Moduleinstellungen
        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();

            $this->SetTimerInterval("Update", $this->ReadPropertyInteger("Aktualisierungsinvertall") * 1000);


            $this->UpdateAuswertung($this->ReadPropertyString("Id"),$this->ReadPropertyString("ApiKey"));
        }

 
        /**
        * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
        * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verfügung gestellt:
        *
        * A_UpdateAuswertung($apikey,$id);
        *
        */
        public function UpdateAuswertung($id, $apikey) {
        
            if ($id != "")
            {

                $apikey = $apikey;
                $id = $id; 
                $content = Sys_GetURLContent("https://monitoringapi.solaredge.com/site/".$id."/overview?api_key=".$apikey);  
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
?>