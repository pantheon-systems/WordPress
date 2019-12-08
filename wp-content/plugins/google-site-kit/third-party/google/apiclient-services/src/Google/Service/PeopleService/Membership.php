<?php

namespace Google\Site_Kit_Dependencies;

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
class Google_Service_PeopleService_Membership extends \Google\Site_Kit_Dependencies\Google_Model
{
    protected $contactGroupMembershipType = 'Google\Site_Kit_Dependencies\Google_Service_PeopleService_ContactGroupMembership';
    protected $contactGroupMembershipDataType = '';
    protected $domainMembershipType = 'Google\Site_Kit_Dependencies\Google_Service_PeopleService_DomainMembership';
    protected $domainMembershipDataType = '';
    protected $metadataType = 'Google\Site_Kit_Dependencies\Google_Service_PeopleService_FieldMetadata';
    protected $metadataDataType = '';
    /**
     * @param Google_Service_PeopleService_ContactGroupMembership
     */
    public function setContactGroupMembership(\Google\Site_Kit_Dependencies\Google_Service_PeopleService_ContactGroupMembership $contactGroupMembership)
    {
        $this->contactGroupMembership = $contactGroupMembership;
    }
    /**
     * @return Google_Service_PeopleService_ContactGroupMembership
     */
    public function getContactGroupMembership()
    {
        return $this->contactGroupMembership;
    }
    /**
     * @param Google_Service_PeopleService_DomainMembership
     */
    public function setDomainMembership(\Google\Site_Kit_Dependencies\Google_Service_PeopleService_DomainMembership $domainMembership)
    {
        $this->domainMembership = $domainMembership;
    }
    /**
     * @return Google_Service_PeopleService_DomainMembership
     */
    public function getDomainMembership()
    {
        return $this->domainMembership;
    }
    /**
     * @param Google_Service_PeopleService_FieldMetadata
     */
    public function setMetadata(\Google\Site_Kit_Dependencies\Google_Service_PeopleService_FieldMetadata $metadata)
    {
        $this->metadata = $metadata;
    }
    /**
     * @return Google_Service_PeopleService_FieldMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
