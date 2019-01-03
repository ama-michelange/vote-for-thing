<?php

namespace Domain\Query;

use Domain\InfraBuilder;
use DomainException;

interface QueryEntityBuilder
{
   /**
    * Base parameters for build the query.
    * @param QueryParams $pQueryParams Base parameters
    * @return QueryEntityBuilder
    */
   public function withParams($pQueryParams) : QueryEntityBuilder;

   /**
    * Verify and build the query with the base parameters.
    * @return void
    * @throws DomainException In case of error in parameters
    */
   public function build() : void;

   /**
    * Return the Infra builder.
    * @return \Domain\InfraBuilder
    */
   public function infraBuilder() : InfraBuilder;
}
