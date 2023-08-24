package com.sms.sendmessage;

import android.os.Bundle;
import androidx.preference.PreferenceFragmentCompat;

class SettingsFragment extends PreferenceFragmentCompat {

    @Override
    public void onCreatePreferences(Bundle savedInstanceState, String rootKey) {
        setPreferencesFromResource(R.xml.root_preferences, rootKey);
    }

}