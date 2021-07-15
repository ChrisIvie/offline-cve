#Author: Christopher Ivie
#Description: Downloads the most recent NIST CVE feed, stores the information into a database!
import requests
import zipfile
import json
import os
import os.path
from os import path
import sqlite3
from datetime import datetime
import pytz
import tzlocal



#Get local time zone
local_timezone = tzlocal.get_localzone()

    
#Creating a step to match the year in the NIST URL.
step = 2003
while step < 2022:
        nextyear = str(step)
        if nextyear == 2021:
            nextyear = "recent"
            print(nextyear)
        step += 1
        print(step)

        #recent vuln feeds from nist
        url = 'https://nvd.nist.gov/feeds/json/cve/1.1/nvdcve-1.1-' + nextyear + '.json.zip'
        target_path = 'recent.zip'
        
        #request feed file
        response = requests.get(url, stream=True)

        #download feed file 
        handle = open(target_path, "wb")
        for chunk in response.iter_content(chunk_size=512):
            if chunk:
                handle.write(chunk)
        handle.close()

        #unzip recent.zip
        with zipfile.ZipFile(target_path, 'r') as zip_ref:
            zip_ref.extractall()
            
        #Remove old zip file
        os.remove("recent.zip")

        #open json file
        with open('nvdcve-1.1-' + nextyear +'.json', 'rb') as f:
            data = json.load(f)

        #database connection
        db = 'cves.db'
        conn = sqlite3.connect(db)
        c = conn.cursor()
        c.execute("CREATE TABLE IF NOT EXISTS cveinfo (CVEID text UNIQUE, lastmodidate json, pubdate json, description json, timestap json, refurl json, name json, refsource json, tags json, metricversion json, vectorstring json, attackvector json, attackcomplexity json, privilegesrequired json, userinteraction json, scope json, confidentialityimpact json, integrityimpact json, availabilityimpact json, basescore json, baseseverity json, CVEsinSet json, cveurl json)") #22

        #BASE of API
        CVEnumof = data['CVE_data_numberOfCVEs']
        numofcveint = int(CVEnumof)
        print(numofcveint)
        CVEtimestamp = data['CVE_data_timestamp']
        print(CVEtimestamp)

        
        count = 1   

        #Write to download log     
        utc_time = datetime.strptime(CVEtimestamp, "%Y-%m-%dT%H:%M%fZ")
        print(utc_time)
        local_time = utc_time.replace(tzinfo=pytz.utc).astimezone(local_timezone)
        firstlog = open("zip-download.log", "a")
        firstlog.write(str("=======================================") + "\n")
        firstlog.write(str("Timestamp: ") + str(local_time) + " MST" + "\n")
        firstlog.write(str("HTTP Response: ") + str(response) + "\n")
        firstlog.write(str("Link location: ") + url + "\n")
        firstlog.write(str("Number of CVEs in this set: ") + str(CVEnumof) + "\n")
        firstlog.close()

                
        #Get the numer of CVEs in set and write to database
        while count < numofcveint:
            try:
                CVEapiroot = data['CVE_Items'][count]
                count += 1
                try:
                    #Start of CVEapiroot['cve']
                    CVEapicve = CVEapiroot['cve']
                    print(CVEapiroot['cve']['CVE_data_meta']['ID'])
                    CVEID = CVEapicve['CVE_data_meta']['ID']
                    CVEdesc = CVEapicve['description']['description_data'][0]['value']
                    CVEurl = CVEapicve['references']['reference_data'][0]['url']
                    CVEname = CVEapicve['references']['reference_data'][0]['name']
                    CVErefsource = CVEapicve['references']['reference_data'][0]['refsource']
                    CVEtags = CVEapicve['references']['reference_data'][0]['tags']
                except IndexError:
                    continue

                #Start of CVEapiroot['impact']['baseMetricV3']['cvssV3']
                CVEapiimpact = CVEapiroot['impact']['baseMetricV3']['cvssV3']
                CVEmetricVersion = CVEapiimpact['version'] 
                CVEvectorString = CVEapiimpact['vectorString'] 
                CVEattackVector = CVEapiimpact['attackVector'] 
                CVEattackComplexity = CVEapiimpact['attackComplexity'] 
                CVEprivilegesRequired = CVEapiimpact['privilegesRequired'] 
                CVEuserInteraction = CVEapiimpact['userInteraction'] 
                CVEscope = CVEapiimpact['scope'] 
                CVEconfidentialityImpact = CVEapiimpact['confidentialityImpact'] 
                CVEintegrityImpact = CVEapiimpact['integrityImpact'] 
                CVEavailabilityImpact = CVEapiimpact['availabilityImpact'] 
                CVEbaseScore = CVEapiimpact['baseScore'] 
                CVEbaseSeverity = CVEapiimpact['baseSeverity'] 
                print(CVEbaseSeverity)
                
                #start of CVEapiroot['lastModifiedDate']
                CVEapilastmodidate = CVEapiroot['lastModifiedDate']
                #start of CVEapiroot['publishedDate']
                CVEapipubdate = CVEapiroot['publishedDate']
                
                
                #print CVEapilastmodidate.strftime("%Y-%m-%dT%H:%M%fZ")
                #print(timemstmodi)
                #print(timemstpub)                
                #Write to database
                c.execute("insert or ignore into cveinfo values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", #22
                        [
                        json.dumps(CVEID),
                        json.dumps(CVEapilastmodidate),
                        json.dumps(CVEapipubdate),
                        json.dumps(CVEdesc),
                        json.dumps(CVEtimestamp),
                        json.dumps(CVEurl),
                        json.dumps(CVEname),
                        json.dumps(CVErefsource),
                        json.dumps(CVEtags),
                        json.dumps(CVEmetricVersion),
                        json.dumps(CVEvectorString),
                        json.dumps(CVEattackVector),
                        json.dumps(CVEattackComplexity),
                        json.dumps(CVEprivilegesRequired),
                        json.dumps(CVEuserInteraction),
                        json.dumps(CVEscope),
                        json.dumps(CVEconfidentialityImpact),
                        json.dumps(CVEintegrityImpact),
                        json.dumps(CVEavailabilityImpact),
                        json.dumps(CVEbaseScore),
                        json.dumps(CVEbaseSeverity),
                        json.dumps(CVEnumof),
                        json.dumps(url),

                        ])
                conn.commit()
            except KeyError:
                continue
        print('nvdcve-1.1-' + nextyear +'.json')
        os.remove('nvdcve-1.1-' + nextyear +'.json')
