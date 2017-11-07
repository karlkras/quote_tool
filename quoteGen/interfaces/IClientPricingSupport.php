<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Axian Developer
 */
interface IClientPricingSupport {
    const COLUMNQUERY = "Select * from %s LIMIT 1";
    const RATE_COLUMN = "rate";
    const RUSH_RATE_COLUMN = "rush_rate";
    
    function supportsRate();
    
    function supportsRushRate();
}
