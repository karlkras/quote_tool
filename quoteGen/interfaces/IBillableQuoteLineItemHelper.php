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
interface IBillableQuoteLineItemHelper extends IBillableQuoteLineItem{
    public function getTargetLangCount();
    public function isDistributed();
}
