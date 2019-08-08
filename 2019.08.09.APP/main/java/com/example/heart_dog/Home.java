package com.example.heart_dog;

import androidx.annotation.NonNull;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.core.view.GravityCompat;
import androidx.drawerlayout.widget.DrawerLayout;
import androidx.fragment.app.FragmentActivity;
import androidx.fragment.app.FragmentTransaction;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.pm.PackageManager;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.ImageButton;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.material.navigation.NavigationView;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.concurrent.ExecutionException;

public class Home extends FragmentActivity {

    ImageButton menu;
    DrawerLayout drawer;
    NavigationView nav;
    JSONObject json;
    String result = "";
    String result_code;
    String usn = MainActivity.USN;
    String MAC = BluetoothChatFragment.mac;
    String device = BluetoothChatFragment.device;
    Intent doginfo, pwchange, main, listVIew;
    TextView heart;

    public static String ssn;
    private static Home ins;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        ins = this;

        setContentView(R.layout.activity_home);
        //@@//
        locationPermissionCheck();

        if (savedInstanceState == null) {
            FragmentTransaction transaction = getSupportFragmentManager().beginTransaction();
            transaction.replace(R.id.content_drawer_menu, new BluetoothChatFragment(Home.this));
            transaction.commit();
        }
        drawer = findViewById(R.id.drawer_layout);
        nav = findViewById(R.id.nav_view);
        heart = findViewById(R.id.tv_heart);

        menu = findViewById(R.id.ib_menu);
        menu.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                drawer.openDrawer(GravityCompat.START);
            }
        });
        activatePolar();

        nav.setNavigationItemSelectedListener(new NavigationView.OnNavigationItemSelectedListener() {
            @Override
            public boolean onNavigationItemSelected(@NonNull MenuItem menuItem) {
                switch (menuItem.getItemId()){
                    case R.id.nav_dog_info: // 강아지 정보 저장을 누른 경우
                        Log.d("Home","Home doginfo is clicked");
                        doginfo = new Intent(getApplicationContext(), DogInfo.class);
                        startActivity(doginfo);
                        break;

                    case R.id.nav_pw_change: // 비밀번호 변경 버튼을 누른 경우
                        pwchange = new Intent(getApplicationContext(), PwChange.class);
                        startActivity(pwchange);
                        break;

                    case R.id.nav_sign_out: // 로그아웃 버튼을 누른 경우
                        json = new JSONObject();
                        try {
                            json.put("USN", usn);
                            Log.d("usn_log", json.toString());
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        try {
                            result = new PostJSON().execute("http://teame-iot.calit2.net/heartdog/app/signout", json.toString()).get();
                        } catch (ExecutionException e) {
                            e.printStackTrace();
                        } catch (InterruptedException e) {
                            e.printStackTrace();
                        }
                        try {
                            JSONObject json_data = new JSONObject(result);
                            Log.d("asdf5", "receive json: " + json_data.toString());
                            result_code = (json_data.optString("result_code"));
                            Log.d("asdf6", "result_code: " + result_code);

                        } catch (Exception e) {
                            Log.e("Fail 3", e.toString());
                        }
                        if(result_code.equals("0")){
                            Toast.makeText(Home.this, "Sign out complete", Toast.LENGTH_SHORT).show();
                            main = new Intent(getApplicationContext(), MainActivity.class);
                            startActivity(main);
                        }
                        else if(result_code.equals("1")){
                            Toast.makeText(Home.this, "Sign out Error", Toast.LENGTH_SHORT).show();
                        }
                        break;

                    case R.id.nav_sensor_regi: // Sensor Registration 메뉴를 누른 경우
                        if(BluetoothChatFragment.bluetoothStatus.equals("1")) { // 센서가 어플리케이션에 연결되어 있는 경우
                            DialogInterface.OnClickListener dialogClickListener = new DialogInterface.OnClickListener() { // dialog 창을 띄움
                                @Override
                                public void onClick(DialogInterface dialog, int which) {
                                    switch (which) {

                                        case DialogInterface.BUTTON_POSITIVE: // Dialog 에서 yes 버튼을 누른 경우
                                            JSONObject json = new JSONObject();
                                            try {
                                                json.put("USN", usn);
                                                json.put("DEVICE", device);
                                                json.put("MAC_ADD", MAC);

                                            } catch (JSONException e) {
                                                e.printStackTrace();
                                            }
                                            try {
                                                result = new PostJSON().execute("http://teame-iot.calit2.net/heartdog/sensor/registration", json.toString()).get();
                                            } catch (ExecutionException e) {
                                                e.printStackTrace();
                                            } catch (InterruptedException e) {
                                                e.printStackTrace();
                                            }
                                            JSONObject json_data = null;
                                            try {
                                                json_data = new JSONObject(result);
                                                Log.d("asdf5", "receive json: " + json_data.toString());
                                                result_code = (json_data.optString("result_code"));
                                                ssn = (json_data.optString("SSN"));
                                                Log.d("asdf6", "result_code, ssn" + result_code + ssn);
                                            } catch (JSONException e) {
                                                e.printStackTrace();
                                            }
                                            if (result_code.equals("0")) { // 등록이 완료된 경우
                                                Toast.makeText(Home.this, "Sensor registration complete", Toast.LENGTH_SHORT).show();
                                                main = new Intent(getApplicationContext(), MainActivity.class);
                                            }
                                            else if (result_code.equals("5")) { // 이미 등록되어있는 기기인 경우
                                                Toast.makeText(Home.this, "Already registrated sensor", Toast.LENGTH_SHORT).show();
                                                main = new Intent(getApplicationContext(), MainActivity.class);
                                            }
                                            else { // 커뮤니케이션 오류
                                                Toast.makeText(Home.this, "Communication Error", Toast.LENGTH_LONG).show();
                                                main = new Intent(getApplicationContext(), MainActivity.class);
                                            }
                                            break;
                                        case DialogInterface.BUTTON_NEGATIVE: // dialog 창에서 no 버튼을 누른 경우
                                            Toast.makeText(Home.this, "Cancel", Toast.LENGTH_LONG).show();
                                            break;
                                    }
                                }
                            };
                            AlertDialog.Builder builder = new AlertDialog.Builder(Home.this);
                            builder.setMessage("Add " + BluetoothChatFragment.device + " to sensor list").setPositiveButton("Yes", dialogClickListener)
                                    .setNegativeButton("No", dialogClickListener).show();
                        }
                        else if(BluetoothChatFragment.bluetoothStatus.equals("2")) { // 센서와 연결되지있지 않은 경우
                            Toast.makeText(Home.this, "Connect device first", Toast.LENGTH_LONG).show();
                        }
                        break;

                    case R.id.nav_sensor_deregi: // Sensor Deregistraion 을 누른 경우
                        JSONObject json = new JSONObject();
                        try {
                            json.put("USN", usn);
                            json.put("SSN", ssn);
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        try {
                            result = new PostJSON().execute("http://teame-iot.calit2.net/heartdog/app/signout", json.toString()).get();
                        } catch (ExecutionException e) {
                            e.printStackTrace();
                        } catch (InterruptedException e) {
                            e.printStackTrace();
                        }
                        JSONObject json_data = null;
                        try {
                            json_data = new JSONObject(result);
                            Log.d("asdf5", "receive json: " + json_data.toString());
                            result_code = (json_data.optString("result_code"));
                            Log.d("asdf6", "result_code" + result_code);
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        if (result_code.equals("0")) { // 해지가 완료된 경우
                            Toast.makeText(Home.this, "Sensor deregistration complete", Toast.LENGTH_SHORT).show();
                        }
                        else if (result_code.equals("6")) { // 등록되어있지 않은 기기인 경우
                            Toast.makeText(Home.this, "Not registered sensor", Toast.LENGTH_SHORT).show();
                        }
                        else { // 커뮤니케이션 오류
                            Toast.makeText(Home.this, "Communication Error", Toast.LENGTH_LONG).show();
                        }
                        break;

                    case R.id.nav_sensor_list:
                        listVIew = new Intent(getApplicationContext(), DeviceListView.class);
                        startActivity(listVIew);
                }
                return true;
            }
        });
    }

    public static Home getInstace(){
        return ins;
    }

    public void updateTheTextView(final String t) {
        Home.this.runOnUiThread(new Runnable() {
            public void run() {
                heart = findViewById(R.id.tv_heart);
                heart.setText(t);
            }
        });
    }

    private final MyPolarBleReceiver mPolarBleUpdateReceiver = new MyPolarBleReceiver() {};

    protected void activatePolar() {
        Log.w(this.getClass().getName(), "activatePolar()");
        registerReceiver(mPolarBleUpdateReceiver, makePolarGattUpdateIntentFilter());
        mPolarBleUpdateReceiver.setCaller(this);
    }
    private static IntentFilter makePolarGattUpdateIntentFilter() {
        final IntentFilter intentFilter = new IntentFilter();
        intentFilter.addAction(MyPolarBleReceiver.ACTION_GATT_CONNECTED);
        intentFilter.addAction(MyPolarBleReceiver.ACTION_GATT_DISCONNECTED);
        intentFilter.addAction(MyPolarBleReceiver.ACTION_HR_DATA_AVAILABLE);
        return intentFilter;
    }
    @Override
    public void onBackPressed() {
        if (drawer.isDrawerOpen(GravityCompat.START)) {
            drawer.closeDrawer(GravityCompat.START);
        } else {
            super.onBackPressed();
        }
    }

    //@@//
    public void locationPermissionCheck() {
        if (Build.VERSION.SDK_INT >= 23 &&
                ContextCompat.checkSelfPermission(Home.this, android.Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(Home.this, new String[]{android.Manifest.permission.ACCESS_FINE_LOCATION},
                    0);
        }
    }
    //@@//
}
