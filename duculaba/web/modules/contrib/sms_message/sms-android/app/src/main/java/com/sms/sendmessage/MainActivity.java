package com.sms.sendmessage;

import android.Manifest;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.Button;
import android.widget.EditText;

import androidx.annotation.NonNull;

import android.telephony.SmsManager;

import androidx.core.app.ActivityCompat;

import android.content.pm.PackageManager;

import androidx.appcompat.widget.Toolbar;

import androidx.core.content.ContextCompat;
import androidx.appcompat.app.AppCompatActivity;


import com.androidnetworking.AndroidNetworking;
import com.androidnetworking.common.Priority;
import com.androidnetworking.error.ANError;
import com.androidnetworking.interfaces.JSONArrayRequestListener;
import com.androidnetworking.interfaces.JSONObjectRequestListener;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;

public class MainActivity extends AppCompatActivity {
    public static final String TAG = "MainActivity";

    static EditText etPhone;
    static EditText etMessage;
    EditText etEndpoint;
    TextView txtURL;
    Button btSend, btSave, btOnService;
    Toolbar toolbar;

    public static final String SHARED_PREFS = "sharedPrefs";
    public static final String URL = "text";

    private static String url;
    private static DBHandler dbHandler;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        toolbar = findViewById(R.id.toolbar);
        //setSupportActionBar(toolbar);

        //todo txtURL must be url in setting
        txtURL = findViewById(R.id.txt_api);

        //Assign variable
        etPhone = findViewById(R.id.et_phone);
        btOnService = findViewById(R.id.btOnService);
        etMessage = findViewById(R.id.et_message);
        btSend = findViewById(R.id.btSend);

        etEndpoint = findViewById(R.id.et_endpoint);
        btSave = findViewById(R.id.btSave);
        dbHandler = new DBHandler(MainActivity.this);

        btSend.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (ContextCompat.checkSelfPermission(MainActivity.this, Manifest.permission.SEND_SMS) == PackageManager.PERMISSION_GRANTED) {
                    String sPhone = etPhone.getText().toString().trim();
                    String sMessage = etMessage.getText().toString().trim();
                    if (sPhone.equals("")) {
                        sPhone = etPhone.getText().toString().trim();
                    }
                    if (sMessage.equals("")) {
                        sMessage = etMessage.getText().toString().trim();
                    }
                    if (!sPhone.equals("") && !sMessage.equals("")) {
                        sendMessage(sPhone, sMessage);
                    } else {
                    }
                } else {
                    ActivityCompat.requestPermissions(MainActivity.this, new String[]{Manifest.permission.SEND_SMS}, 100);
                }
            }
        });

        btSave.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                url = etEndpoint.getText().toString();
                txtURL.setText(url);
                saveData();
            }
        });
        btOnService.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                startService();
            }
        });
        dbHandler = new DBHandler(MainActivity.this);
        loadData();
        updateViewsUrl();
    }

    public void startService() {
        Toast.makeText(getApplicationContext(), "On Click", Toast.LENGTH_LONG).show();
        startService(new Intent(this, SampleService.class));
    }

    public static void sendMessage(String sPhone, String sMessage) {
        SmsManager smsManager = SmsManager.getDefault();
        smsManager.sendTextMessage(sPhone, null, sMessage, null, null);
    }

    public static void startHandlerThread() {
        final Handler handler = new Handler();
        Runnable runnable = new Runnable() {

            @Override
            public void run() {
                try {
                    SimpleDateFormat sdh = new SimpleDateFormat("HH");
                    SimpleDateFormat sdm = new SimpleDateFormat("mm");
                    String currentHTime = sdh.format(new Date());
                    String currentMTime = sdm.format(new Date());
                    if (currentHTime.equals("00") && (currentMTime.equals("00") || currentMTime.equals("01"))) {
                        dbHandler.resetDB();
                    }
                    callAPI();
                } catch (Exception e) {
                    // TODO: handle exception.
                } finally {
                    // TODO: Pause time - make variable in settings.
                    handler.postDelayed(this, 60000);
                }
            }
        };
        handler.post(runnable);
    }

    private static void callAPI() {
        AndroidNetworking.get(url)
        .setPriority(Priority.LOW)
        .build()
        .getAsJSONArray(new JSONArrayRequestListener() {
            @Override
            public void onResponse(JSONArray response) {
                try {
                    ArrayList<Data> dataArrayList = new ArrayList<Data>();
                    for (int i = 0; i < response.length(); i++) {
                        JSONObject O = response.getJSONObject(i);
                        String message = O.getString("message");
                        String number = O.getString("number");
                        String uuid = O.getString("uuid");
                        Data data = new Data(message, number, uuid);
                        dataArrayList.add(data);
                        dbHandler.addNewCourse(uuid, message, number);
                    }
                    sendAllMess(dataArrayList);
                } catch (JSONException e) {
                    e.printStackTrace();
                }
            }

            @Override
            public void onError(ANError anError) {
                Log.e("AndroidNetworking", "onResponse: " + anError.toString());
            }
        });
    }

    private static void callAPIPost(String uuid, String mess, String phone) {
        AndroidNetworking.post(url)
        .addBodyParameter("uuid", uuid)
        .setPriority(Priority.LOW)
        .build()
        .getAsJSONObject(new JSONObjectRequestListener() {
            @Override
            public void onResponse(JSONObject response) {
                dbHandler.addNewCourse(uuid, mess, phone);
            }

            @Override
            public void onError(ANError anError) {

            }
        });
    }

    private static void sendAllMess(ArrayList<Data> dataArrayList) {
        for (int i = 0; i < dataArrayList.size(); i++) {
            Data data = dataArrayList.get(i);
            ArrayList<Data> dataArray = dbHandler.getAllProducts();
            int check = 0;
            for (int j = 0; j < dataArray.size(); j++) {
                if (dataArray.get(j).getUuid().equals(data.getUuid())) {
                    check++;
                    break;
                }
            }
            if (check == 0) {
                sendMessage(data.getNumber(), data.getMessage());
                callAPIPost(data.getUuid(), data.getMessage(), data.getNumber());
            }
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == 100 && grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
            String sPhone = etPhone.getText().toString().trim();
            String sMessage = etMessage.getText().toString().trim();
            if (sPhone.equals("")) {
                sPhone = etPhone.getText().toString().trim();
            }
            if (sMessage.equals("")) {
                sMessage = etMessage.getText().toString().trim();
            }
            if (!sPhone.equals("") && !sMessage.equals("")) {
                sendMessage(sPhone, sMessage);
            } else {
            }
        } else {
            Toast.makeText(getApplicationContext(), "Permission Denied", Toast.LENGTH_LONG).show();
        }
    }

    public void saveData() {
        SharedPreferences sharedPreferences = getSharedPreferences(SHARED_PREFS, MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putString(URL, url);
        editor.apply();
        Toast.makeText(this, "Data Save", Toast.LENGTH_LONG);
    }

    public void loadData() {
        SharedPreferences sharedPreferences = getSharedPreferences(SHARED_PREFS, MODE_PRIVATE);
        url = sharedPreferences.getString(URL, "");
    }

    public void updateViewsUrl() {
        txtURL.setText(url);
    }
}