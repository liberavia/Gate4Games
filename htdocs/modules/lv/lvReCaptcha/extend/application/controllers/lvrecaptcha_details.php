<?php

/*
 * Copyright (C) 2015 André Gregor-Herrmann
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of lvrecaptcha_details
 *
 * @author Gate4Games
 * @author André Gregor-Herrmann
 */
class lvrecaptcha_details extends lvrecaptcha_details_parent {

    /**
     * Saves user ratings and review text (oxReview object)
     *
     * @return null
     */
    public function saveReview()
    {
        if (!oxRegistry::getSession()->checkSessionChallenge()) {
            return;
        }

        if ($this->canAcceptFormData() &&
            ($oUser = $this->getUser()) && ($oProduct = $this->getProduct())
        ) {

            $dRating = $this->getConfig()->getRequestParameter('artrating');
            if ($dRating !== null) {
                $dRating = (int) $dRating;
            }

            //save rating
            if ($dRating !== null && $dRating >= 1 && $dRating <= 5) {
                $oRating = oxNew('oxrating');
                if ($oRating->allowRating($oUser->getId(), 'oxarticle', $oProduct->getId())) {
                    $oRating->oxratings__oxuserid = new oxField($oUser->getId());
                    $oRating->oxratings__oxtype = new oxField('oxarticle');
                    $oRating->oxratings__oxobjectid = new oxField($oProduct->getId());
                    $oRating->oxratings__oxrating = new oxField($dRating);
                    $oRating->save();
                    $oProduct->addToRatingAverage($dRating);
                }
            }

            if (($sReviewText = trim(( string ) $this->getConfig()->getRequestParameter('rvw_txt', true)))) {
                $oReview = oxNew('oxReview');
                $oReview->oxreviews__oxobjectid = new oxField($oProduct->getId());
                $oReview->oxreviews__oxtype = new oxField('oxarticle');
                $oReview->oxreviews__oxtext = new oxField($sReviewText, oxField::T_RAW);
                $oReview->oxreviews__oxlang = new oxField(oxRegistry::getLang()->getBaseLanguage());
                $oReview->oxreviews__oxuserid = new oxField($oUser->getId());
                $oReview->oxreviews__oxrating = new oxField(($dRating !== null) ? $dRating : 0);
                $oReview->save();
            }
        }
    }
}
