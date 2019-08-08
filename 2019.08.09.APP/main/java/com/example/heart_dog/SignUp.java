package com.example.heart_dog;

import androidx.appcompat.app.AppCompatActivity;
import androidx.constraintlayout.widget.Group;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.concurrent.ExecutionException;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class SignUp extends AppCompatActivity {

    EditText email, password, password_check, fname, lname, phone;
    String email_str, pw_str, pw_check_str, fname_str, lname_str, phone_str;
    String result = "";
    TextView tv1;
    Button sign;
    JSONObject jsonObject, signup_result_json;
    String result_code;
    String url = "http://teamd-iot.calit2.net/app/signup";
    Intent intent1;
    boolean b1;
    public static final Pattern VALID_PASSWOLD_REGEX_ALPHA_NUM = Pattern.compile("^(?=.*[A-Za-z])(?=.*\\d)(?=.*[$@$!%*#?&])[A-Za-z\\d$@$!%*#?&]{8,16}$"); // 8자리 ~ 16자리까지 가능

    public String getEmail ()
    {
        return email_str;
    }

    public void setEmail (String email_str)
    {
        this.email_str = email_str;
    }

    public String getFirstName ()
    {
        return fname_str;
    }

    public void setFirstName (String fname_str)
    {
        this.fname_str = fname_str;
    }

    public String getPhoneNumber ()
    {
        return phone_str;
    }

    public void setPhoneNumber (String phone_str)
    {
        this.phone_str = phone_str;
    }

    public String getLastName ()
    {
        return lname_str;
    }

    public void setLastName (String lname_str)
    {
        this.lname_str = lname_str;
    }

    public String getPassword ()
    {
        return pw_str;
    }

    public void setPassword (String pw_str)
    {
        this.pw_str = pw_str;
    }

    public String getCheckPassword ()
    {
        return pw_check_str;
    }

    public void setCheckPassword (String pw_check_str)
    {
        this.pw_check_str = pw_check_str;
    }
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_sign_up);

        email = findViewById(R.id.et_email);
        password = findViewById(R.id.et_pw);
        password_check = findViewById(R.id.et_pw_check);
        fname = findViewById(R.id.et_first);
        lname = findViewById(R.id.et_last);
        phone = findViewById(R.id.et_phone);

        tv1 = (TextView)findViewById(R.id.tv_pwInfo);

        password.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                pw_str = password.getText().toString();
                b1 = validatePassword(pw_str);

                if(b1 == true) {
                    tv1.setText("* Available Password");
                }
                else {
                    tv1.setText("* Must contain 1 letters, 1 number, 1 special character, between 8-16 long");
                }
            }
            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                pw_str = password.getText().toString();
                b1 = validatePassword(pw_str);

                if(b1 == true) { // 일치하는 경우
                    tv1.setTextColor(Color.BLACK);
                    tv1.setText("* Available Password");
                }
                else { // 일치하지 않는 경우
                    tv1.setText("* Must contain 1 letters, 1 number, 1 special character, between 8-16 long");
                    tv1.setTextColor(Color.RED);
                }
            }
            @Override
            public void afterTextChanged(Editable editable) { // 같이 입력된 후에 확인
            }
        });

        sign = findViewById(R.id.btn_sign); // Sign 버튼을 누른 경우
        sign.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // 텍스트에 값을 설정
                setEmail(email.getText().toString().trim());
                setPassword(password.getText().toString().trim());
                setCheckPassword(password_check.getText().toString().trim());
                setFirstName(fname.getText().toString().trim());
                setLastName(lname.getText().toString().trim());
                setPhoneNumber(phone.getText().toString().trim());

                if(getEmail().isEmpty()  || getPassword().isEmpty()  || getCheckPassword().isEmpty()  || getFirstName().isEmpty()  || getLastName().isEmpty() || getPhoneNumber().isEmpty()) {
                    if(getEmail().isEmpty()) {
                        Toast.makeText(SignUp.this, "Input your E-mail Address.",Toast.LENGTH_SHORT).show();
                    }else if(getPassword().isEmpty()  ){
                        Toast.makeText(SignUp.this, "Input your Password.",Toast.LENGTH_SHORT).show();
                    }else if(getCheckPassword().isEmpty() ){
                        Toast.makeText(SignUp.this, "Input your Password Confirmation.",Toast.LENGTH_SHORT).show();
                    }
                    else if(getFirstName().isEmpty()){
                        Toast.makeText(SignUp.this, "Input your First Name.",Toast.LENGTH_SHORT).show();
                    }
                    else if(getLastName().isEmpty()){
                        Toast.makeText(SignUp.this, "Input your Last Name.",Toast.LENGTH_SHORT).show();
                    }else{
                        Toast.makeText(SignUp.this, "Input your phone number",Toast.LENGTH_SHORT).show();
                    }
                }else{
                    //Check Password
                    if(pw_str.equals(pw_check_str)){

                        JSONObject jsonObject = new JSONObject();
                        try {
                            jsonObject.put("Email", getEmail());
                            jsonObject.put("Password", getPassword());
                            jsonObject.put("PhoneNumber", getPhoneNumber());
                            jsonObject.put("FirstName", getFirstName());
                            jsonObject.put("LastName", getLastName());

                            Log.d("asdf1", jsonObject.toString());
                        } catch (JSONException e) {
                            e.printStackTrace();
                        }
                        if (email.length() > 0) {
                            try {
                                Log.d("asdf2", jsonObject.toString());
                                result = new PostJSON().execute("http://teame-iot.calit2.net/heartdog/app/signup", jsonObject.toString()).get();
                                Log.d("asdf3", result);
                            } catch (ExecutionException e) {
                                e.printStackTrace();
                            }catch (Exception e) {
                                Log.d("asdf411", e.toString());
                                e.printStackTrace();
                            }
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
                            Toast.makeText(SignUp.this, "Autohrized mail is sent ! \nPlease Check your E-mail", Toast.LENGTH_SHORT).show();
                            intent1 = new Intent(getApplicationContext(), MainActivity.class);
                            startActivity(intent1);
                        }
                        else if(result_code.equals("1")){
                            Toast.makeText(SignUp.this, "Already exist E-mail \nPlease check", Toast.LENGTH_SHORT).show();
                        }
                    }else{
                        Toast.makeText(SignUp.this, "Please Check your password.",Toast.LENGTH_SHORT).show();
                    }
                }
            }
        });
    }
    public static boolean validatePassword(String pwStr) { // 값을 비교해주는 함수
        Matcher matcher = VALID_PASSWOLD_REGEX_ALPHA_NUM.matcher(pwStr);
        return matcher.matches();
    }
}
