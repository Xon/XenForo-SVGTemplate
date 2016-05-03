<?php

class SV_SVGTemplate_Option_ToggleListener
{
    public static function verifyOption(&$option, XenForo_DataWriter $dw, $fieldName)
    {
        $db = XenForo_Application::getDB();
        $id = $db->fetchOne("
            SELECT event_listener_id
            FROM xf_code_event_listener
            WHERE addon_id = 'SV_SVGTemplate' and event_id = 'front_controller_pre_view'
        ");
        if ($id)
        {
            $listener = XenForo_DataWriter::Create("XenForo_DataWriter_CodeEventListener");
            $listener->setExistingData($id);
            $listener->set('active', $option ? 1 : 0);
            $listener->save();
        }

        return true;
    }
}