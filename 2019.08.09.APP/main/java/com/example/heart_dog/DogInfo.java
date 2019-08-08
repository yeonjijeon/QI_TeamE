package com.example.heart_dog;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.Toast;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.concurrent.ExecutionException;

public class DogInfo extends AppCompatActivity {

    Spinner type, gender;
    String Type, Gender, Name, Birth;
    String result = "";
    String result_code;
    EditText name, birth;
    Button save;
    JSONObject json;
    Intent intent1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_dog_info);

        type = findViewById(R.id.sp_dog_type);
        gender = findViewById(R.id.sp_dog_gender);
        name = findViewById(R.id.et_dog_name);
        birth = findViewById(R.id.et_dog_birth);

        Name = name.getText().toString().trim();
        Birth = birth.getText().toString().trim();
        Type = type.getSelectedItem().toString();
        Gender = type.getSelectedItem().toString();

        save = findViewById(R.id.btn_save);
        save.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                json = new JSONObject();

                JSONObject jsonObject = new JSONObject();
                try {
                    jsonObject.put("Name", Name);
                    jsonObject.put("Type", Type);
                    jsonObject.put("Birth", Birth);
                    jsonObject.put("Gender", Gender);

                    Log.d("asdf1", jsonObject.toString());
                } catch (JSONException e) {
                    e.printStackTrace();
                }
                try {
                    Log.d("asdf2", jsonObject.toString());
                    result = new PostJSON().execute("http://teame-iot.calit2.net/heartdog/app/doginfo", jsonObject.toString()).get();
                    Log.d("asdf3", result);
                } catch (ExecutionException e) {
                    e.printStackTrace();
                } catch (Exception e) {
                    Log.d("asdf411", e.toString());
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
                    Toast.makeText(DogInfo.this, "Save your dog Information", Toast.LENGTH_SHORT).show();
                    intent1 =  new Intent(getApplicationContext(), Home.class);
                }
                else {
                    Toast.makeText(DogInfo.this, "Error appeared", Toast.LENGTH_SHORT).show();
                }
            }
        });
    }
}