package com.example.heart_dog;

import android.app.Activity;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.util.Log;

import java.util.StringTokenizer;

public class MyPolarBleReceiver extends BroadcastReceiver {
    public final static String ACTION_GATT_CONNECTED =
            "edu.ucsd.healthware.fw.device.ble.ACTION_GATT_CONNECTED";
    public final static String ACTION_GATT_DISCONNECTED =
            "edu.ucsd.healthware.fw.device.ble.ACTION_GATT_DISCONNECTED";
    public final static String ACTION_HR_DATA_AVAILABLE =
            "edu.ucsd.healthware.fw.device.ble.ACTION_HR_DATA_AVAILABLE";
    public final static String EXTRA_DATA =
            "edu.ucsd.healthware.fw.device.ble.EXTRA_DATA";

    Home caller;

    public void setCaller(Home caller) {
        this.caller = caller;
    }

    @Override
    public void onReceive(Context ctx, Intent intent) {
        final String action = intent.getAction();
        if (ACTION_GATT_CONNECTED.equals(action)) {
            Log.w(this.getClass().getName(), "####ACTION_GATT_CONNECTED");
        } else if (ACTION_GATT_DISCONNECTED.equals(action)) {
        } else if (ACTION_HR_DATA_AVAILABLE.equals(action)) {
            //broadcastUpdate(ACTION_HR_DATA_AVAILABLE, heartRate+";"+pnnPercentage+";"+pnnCount+";"+rrThreshold+";"+bioHarnessSessionData.totalNN+";"+bioHarnessSessionData.lastRRvalue+";"+bioHarnessSessionData.sessionId);
            String data = intent.getStringExtra(EXTRA_DATA);
            StringTokenizer tokens = new StringTokenizer(data, ";");
            String heartRate = tokens.nextToken();
            int pnnPercentage = Integer.parseInt(tokens.nextToken());
            int pnnCount = Integer.parseInt(tokens.nextToken());
            int rrThreshold = Integer.parseInt(tokens.nextToken());
            int totalNN = Integer.parseInt(tokens.nextToken());
            int lastRRvalue = Integer.parseInt(tokens.nextToken());
            String sessionId = tokens.nextToken();
            Log.w(this.getClass().getName(), "####Received heartRate: " +heartRate+" pnnPercentage: "+pnnPercentage+" pnnCount: "+pnnCount+" rrThreshold: "+rrThreshold+" totalNN: "+totalNN+" lastRRvalue: "+lastRRvalue+" sessionId: "+sessionId);

            try {
                Home.getInstace().updateTheTextView(heartRate);
            } catch (Exception e) {

            }
        }
    }

}