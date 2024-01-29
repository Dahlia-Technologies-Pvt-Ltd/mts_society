import React, { useState, useEffect } from "react";
import {
    Grid,
    FormControlLabel,
    Button,
    RadioGroup,
    Typography,
    FormGroup,
    Switch,
    Box,
    Paper,
    Alert,
    AlertTitle,
} from "@mui/material";
import { Portal } from '@mui/base';
import Snackbar from "@mui/material/Snackbar";
import { useParams, Link, useNavigate } from "react-router-dom";
import CustomRadio1 from "./CustomRadio1";
import PageContainer from "@src/components/container/PageContainer";
import Breadcrumb from "@src/layouts/full/shared/breadcrumb/Breadcrumb2";
import CustomTextField from "@src/components/forms/theme-elements/CustomTextField";
import axios from "axios";
import * as yup from "yup";
import { useFormik } from "formik";
import "react-quill/dist/quill.snow.css";
import { useTheme } from "@mui/material/styles";
import ReactQuill from "react-quill";
import CustomFormLabel from "@src/components/forms/theme-elements/CustomFormLabel";
import ParentCard from "@src/components/shared/ParentCard";


const SystemSettings = () => {
    const [isSuccessVisible, setIsSuccessVisible] = useState(false);
  const [successMessage, setSuccessMessage] = useState('');
  const [isErrorVisible, setIsErrorVisible] = useState(false);
  const [errorMessage, setErrorMessage] = useState('');
    const { id } = useParams(); // Access the 'id' parameter from the URL if it exists
    const navigate = useNavigate();

    const [checked, setChecked] = React.useState(true);

    const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        setChecked(event.target.checked);
    };

    const validationSchema = yup.object().shape({
        mail_mailer: yup.string().required("Mailer Name is required"),
        mail_host: yup.string().required("Host Name is required"),
        mail_port: yup.string().required("Port is required"),
        mail_username: yup.string().required("Username is required"),
        mail_password: yup.string().required("Password is required"),
        mail_from_address: yup
            .string()
            .required("From Email Address is required").email("Enter a valid email"),
        mail_from_name: yup.string().required("Sender Name is required"),
        mail_ssl_enable: yup.string().required("SSL Value is required"),
        google_analytics_key: yup
            .string()
            .required("Google Location Key is required"),
    });
    const formik = useFormik(
        {
        initialValues: {
            mail_mailer: "",
            mail_host: "",
            mail_port: "",
            mail_username: "",
            mail_password: "",
            mail_from_address: "",
            mail_from_name: "",
            google_analytics_key: "",
            mail_ssl_enable: "yes",
        },
        //validationSchema,
        onSubmit: async (values) => {
            try {
                // Create a FormData object
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append("mail_host", values.mail_host);
                formData.append("mail_mailer", values.mail_mailer);
                formData.append("mail_port", values.mail_port);
                formData.append("mail_username", values.mail_username);
                {checked ? ("") : (
                    formData.append("mail_password", values.mail_password))}
                formData.append("mail_from_address", values.mail_from_address);
                formData.append("mail_from_name", values.mail_from_name);
                formData.append(
                    "google_analytics_key",
                    values.google_analytics_key
                );
                formData.append("support_email", values.mail_from_address);
                formData.append("mail_ssl_enable", values.mail_ssl_enable);
                const bool = (checked ? "1" : "0")
                formData.append("mail_through_ip", bool);

                // Determine the API URL based on whether it's an edit or add operation
                const appUrl = import.meta.env.VITE_API_URL;
                let API_URL = appUrl + "/api/edit-system-settings";
                if (id) {
                    formData.append("id", id);
                }

                // Make the API POST request
                const token = localStorage.getItem("authToken");
                const response = await axios.post(API_URL, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "multipart/form-data",
                    },
                });
                //console.log('API Response:', response.data);
                sessionStorage.setItem("successMessage", response.data.message);
                setIsSuccessVisible(true);
                setSuccessMessage(response.data.message);
            } catch (error) {
                // Handle errors here
                setIsErrorVisible(true);
                if (error.response && error.response.data && error.response.data.validation_error) {
                    const validationErrors = error.response.data.validation_error;
                  const errorMessages = [];
          
                // Iterate through each field in the validation_errors object
                for (const field in validationErrors) {
                if (validationErrors.hasOwnProperty(field)) {
                    // Get the error message for the field
                    const errorMessage = validationErrors[field][0];
        
                    // Add the error message to the array
                    errorMessages.push(errorMessage);
                }
                // Now, you have an array (errorMessages) that contains all error messages.
                // You can concatenate them into a single message, separated by line breaks or commas.
                const concatenatedErrorMessage = errorMessages.join('\n');
                setErrorMessage(concatenatedErrorMessage);
                }
                }
                else if(error.response.data.message) {
                setErrorMessage(error.response.data.message);
                } else {
                setErrorMessage('An error occurred while changing the password.');
                }
            }
        },
    });

    // Fetch existing data if it's an edit operation
    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = `${appUrl}/api/show-system-settings`;
            const token = localStorage.getItem("authToken");

            const response = await axios.post(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });

            const data = response.data;
            console.log("edit data", data);

            const temp_ssl = (data.data.mail_ssl_enable == "yes") ? "yes" : "no";
            const bool = (data.data.mail_through_ip == "0") ? false : true;
            setChecked(bool);
            if (data.success == true) {
                formik.setValues({
                    mail_mailer: data.data.mail_mailer,
                    mail_host: data.data.mail_host,
                    mail_port: data.data.mail_port,
                    mail_username: data.data.mail_username,
                    mail_password: data.data.mail_password,
                    mail_from_address: data.data.mail_from_address,
                    mail_from_name: data.data.mail_from_name,
                    google_analytics_key: data.data.google_analytics_key,
                    mail_ssl_enable: temp_ssl,
                });
            }
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    const handleCancel = () => {
        fetchData();
    }

    return (
        <>
            <PageContainer
                title="System Settings"
                description="This is System Settings"
            >
                {/* breadcrumb */}
                <Breadcrumb title="" />
                {/* end breadcrumb */}


                <Portal>
            <Snackbar
                anchorOrigin={{ vertical: "top", horizontal: "right" }}
                open={isErrorVisible}
                autoHideDuration={3000}
                onClose={() => setIsErrorVisible(false)}
            >
                <Alert severity="error">
                    <div style={{ fontSize: "14px", padding: "2px" }}>
                        {errorMessage && <div>{errorMessage}</div>}
                    </div>
                </Alert>
            </Snackbar>
            </Portal>
            <Portal>
            <Snackbar
                anchorOrigin={{ vertical: "top", horizontal: "right" }}
                open={isSuccessVisible}
                autoHideDuration={3000}
                onClose={() => setIsSuccessVisible(false)}
            >
                <Alert severity="success">
                    <div style={{ fontSize: "14px", padding: "2px" }}>
                        {successMessage && <div>{successMessage}</div>}
                    </div>
                </Alert>
            </Snackbar>
            </Portal>
                <ParentCard title={"System Settings"}>
                <Grid item xs={12} alignItems="center" md={2} p={2}>
                <FormGroup>
                <CustomFormLabel
                                        sx={{ mt: 0 }}
                                    >
                                        Set IP Setting
                                    </CustomFormLabel>
                <FormControlLabel control={
                    
                    <Switch
                    checked={checked}
                    onChange={handleChange}
                    inputProps={{ 'aria-label': 'controlled' }}
                  />
                } label="" />
                </FormGroup>
                </Grid>
                    <form onSubmit={formik.handleSubmit}>
                        <Grid container>
                            {/* 1 */}
                            <Grid item xs={12} alignItems="center" p={2}>
                                <Grid
                                    item
                                    xs={12}
                                    display="flex"
                                    alignItems="center"
                                >
                                    <CustomFormLabel
                                        htmlFor="mail_mailer"
                                        sx={{ mt: 0 }}
                                    >
                                        Mailer Name
                                    </CustomFormLabel>
                                    <Typography
                                    variant="h5"
                                    sx={{
                                        color: (theme) =>
                                            theme.palette.error.main,
                                        marginLeft: "3px",
                                    }}
                                >
                                    {" "}
                                    *
                                </Typography>
                                </Grid>
                                <Grid item xs={12} mt={1}>
                                    <CustomTextField
                                        id="mail_mailer"
                                        name="mail_mailer"
                                        placeholder="Mailer"
                                        fullWidth
                                        value={formik.values.mail_mailer}
                                        onChange={formik.handleChange}
                                        onKeyPress={(e) => {
                                            if (
                                                e.target.value.length >= 35
                                            ) {
                                                e.preventDefault();
                                            }
                                        }}
                                        error={
                                            formik.touched.mail_mailer &&
                                            Boolean(formik.errors.mail_mailer)
                                        }
                                        helperText={
                                            formik.touched.mail_mailer &&
                                            formik.errors.mail_mailer
                                        }
                                    />
                                </Grid>
                            </Grid>

                            {/* 2 */}
                            <Grid item xs={12} alignItems="center" md={6} p={2}>
                                <Grid
                                    item
                                    xs={12}
                                    display="flex"
                                    alignItems="center"
                                >
                                    <CustomFormLabel
                                        htmlFor="mail_host"
                                        sx={{ mt: 0 }}
                                    >
                                        Email Host
                                    </CustomFormLabel>
                                    <Typography
                                    variant="h5"
                                    sx={{
                                        color: (theme) =>
                                            theme.palette.error.main,
                                        marginLeft: "3px",
                                    }}
                                >
                                    {" "}
                                    *
                                </Typography>
                                </Grid>
                                <Grid item xs={12} mt={1}>
                                    <CustomTextField
                                        id="mail_host"
                                        name="mail_host"
                                        placeholder="Email Host"
                                        fullWidth
                                        value={formik.values.mail_host}
                                        onChange={formik.handleChange}
                                        onKeyPress={(e) => {
                                            if (
                                                e.target.value.length >= 35
                                            ) {
                                                e.preventDefault();
                                            }
                                        }}
                                        error={
                                            formik.touched.mail_host &&
                                            Boolean(formik.errors.mail_host)
                                        }
                                        helperText={
                                            formik.touched.mail_host &&
                                            formik.errors.mail_host
                                        }
                                    />
                                </Grid>
                            </Grid>

                            {/* 3 */}
                            <Grid item xs={12} alignItems="center" md={3} p={2}>
                                <Grid
                                    item
                                    xs={12}
                                    display="flex"
                                    alignItems="center"
                                >
                                    <CustomFormLabel
                                        htmlFor="mail_port"
                                        sx={{ mt: 0 }}
                                    >
                                        Port Name
                                    </CustomFormLabel>
                                    <Typography
                                    variant="h5"
                                    sx={{
                                        color: (theme) =>
                                            theme.palette.error.main,
                                        marginLeft: "3px",
                                    }}
                                >
                                    {" "}
                                    *
                                </Typography>
                                </Grid>
                                <Grid item xs={12} mt={1}>
                                    <CustomTextField
                                        id="mail_port"
                                        name="mail_port"
                                        placeholder="Port Name"
                                        fullWidth
                                        value={formik.values.mail_port}
                                        onChange={formik.handleChange}
                                        onKeyPress={(e) => {
                                            if (
                                                e.target.value.length >= 3 ||
                                                e.key === "e" ||
                                                e.key === "-" ||
                                                e.key === " " ||
                                                e.key === "+" ||
                                                isNaN(Number(e.key))
                                            ) {
                                                e.preventDefault();
                                            }
                                        }}
                                        error={
                                            formik.touched.mail_port &&
                                            Boolean(formik.errors.mail_port)
                                        }
                                        helperText={
                                            formik.touched.mail_port &&
                                            formik.errors.mail_port
                                        }
                                    />
                                </Grid>
                            </Grid>

                            <Grid item xs={12} alignItems="center" md={3} p={2}>
                                <Grid
                                    item
                                    xs={12}
                                    display="flex"
                                    alignItems="center"
                                >
                                    <CustomFormLabel
                                        htmlFor="mail_ssl_enable"
                                        sx={{ mt: 0 }}
                                    >
                                        Enable SSL
                                    </CustomFormLabel>
                                    <Typography
                                    variant="h5"
                                    sx={{
                                        color: (theme) =>
                                            theme.palette.error.main,
                                        marginLeft: "3px",
                                    }}
                                >
                                    {" "}
                                    *
                                </Typography>
                                </Grid>
                                <Grid item xs={12} mt={1}>
                                <RadioGroup
                                    row
                                    aria-label="mail_ssl_enable"
                                    name="mail_ssl_enable"
                                    value={
                                        formik.values.mail_ssl_enable
                                    }
                                    onChange={formik.handleChange}
                                >
                                    <FormControlLabel
                                        value="yes"
                                        control={<CustomRadio1 />}
                                        label="Yes"
                                    />
                                    <FormControlLabel
                                        value="no"
                                        control={<CustomRadio1 />}
                                        label="No"
                                    />
                                </RadioGroup>
                            </Grid>
                            </Grid>

                            {/* 4 */}
                            <Grid item xs={12} alignItems="center" md={6} p={2}>
                                <Grid
                                    item
                                    xs={12}
                                    display="flex"
                                    alignItems="center"
                                >
                                    <CustomFormLabel
                                        htmlFor="mail_username"
                                        sx={{ mt: 0 }}
                                    >
                                        Email Username
                                    </CustomFormLabel>
                                    <Typography
                                    variant="h5"
                                    sx={{
                                        color: (theme) =>
                                            theme.palette.error.main,
                                        marginLeft: "3px",
                                    }}
                                >
                                    {" "}
                                    *
                                </Typography>
                                </Grid>
                                <Grid item xs={12} mt={1}>
                                    <CustomTextField
                                        id="mail_username"
                                        name="mail_username"
                                        placeholder="Email Username"
                                        fullWidth
                                        value={formik.values.mail_username}
                                        onChange={formik.handleChange}
                                        onKeyPress={(e) => {
                                            if (
                                                e.target.value.length >= 35
                                            ) {
                                                e.preventDefault();
                                            }
                                        }}
                                        error={
                                            formik.touched.mail_username &&
                                            Boolean(formik.errors.mail_username)
                                        }
                                        helperText={
                                            formik.touched.mail_username &&
                                            formik.errors.mail_username
                                        }
                                    />
                                </Grid>
                            </Grid>
                            

                            {/* 5 */}
                            <Grid item xs={12} alignItems="center" md={6} p={2}>
                                {checked ? ("") : (<>
                                <Grid
                                    item
                                    xs={12}
                                    display="flex"
                                    alignItems="center"
                                >
                                    <CustomFormLabel
                                        htmlFor="mail_password"
                                        sx={{ mt: 0 }}
                                    >
                                        Email Password
                                    </CustomFormLabel>
                                    <Typography
                                    variant="h5"
                                    sx={{
                                        color: (theme) =>
                                            theme.palette.error.main,
                                        marginLeft: "3px",
                                    }}
                                >
                                    {" "}
                                    *
                                </Typography>
                                </Grid>
                                <Grid item xs={12} mt={1}>
                                    <CustomTextField
                                        id="mail_password"
                                        name="mail_password"
                                        placeholder="Email Password"
                                        fullWidth
                                        value={formik.values.mail_password}
                                        onChange={formik.handleChange}
                                        onKeyPress={(e) => {
                                            if (
                                                e.target.value.length >= 35
                                            ) {
                                                e.preventDefault();
                                            }
                                        }}
                                        error={
                                            formik.touched.mail_password &&
                                            Boolean(formik.errors.mail_password)
                                        }
                                        helperText={
                                            formik.touched.mail_password &&
                                            formik.errors.mail_password
                                        }
                                    />
                                </Grid></>)}
                            </Grid>

                            {/* 6 */}
                            <Grid item xs={12} alignItems="center" md={6} p={2}>
                                <Grid
                                    item
                                    xs={12}
                                    display="flex"
                                    alignItems="center"
                                >
                                    <CustomFormLabel
                                        htmlFor="mail_from_address"
                                        sx={{ mt: 0 }}
                                    >
                                        Email From Address
                                    </CustomFormLabel>
                                    <Typography
                                    variant="h5"
                                    sx={{
                                        color: (theme) =>
                                            theme.palette.error.main,
                                        marginLeft: "3px",
                                    }}
                                >
                                    {" "}
                                    *
                                </Typography>
                                </Grid>
                                <Grid item xs={12} mt={1}>
                                    <CustomTextField
                                        id="mail_from_address"
                                        name="mail_from_address"
                                        placeholder="Email From"
                                        fullWidth
                                        value={formik.values.mail_from_address}
                                        onChange={formik.handleChange}
                                        error={
                                            formik.touched.mail_from_address &&
                                            Boolean(
                                                formik.errors.mail_from_address
                                            )
                                        }
                                        helperText={
                                            formik.touched.mail_from_address &&
                                            formik.errors.mail_from_address
                                        }
                                    />
                                </Grid>
                            </Grid>

                            {/* 7 */}
                            <Grid item xs={12} alignItems="center" md={6} p={2}>
                                <Grid
                                    item
                                    xs={12}
                                    display="flex"
                                    alignItems="center"
                                >
                                    <CustomFormLabel
                                        htmlFor="mail_from_name"
                                        sx={{ mt: 0 }}
                                    >
                                        Email From Name
                                    </CustomFormLabel>
                                    <Typography
                                    variant="h5"
                                    sx={{
                                        color: (theme) =>
                                            theme.palette.error.main,
                                        marginLeft: "3px",
                                    }}
                                >
                                    {" "}
                                    *
                                </Typography>
                                </Grid>
                                <Grid item xs={12} mt={1}>
                                    <CustomTextField
                                        id="mail_from_name"
                                        name="mail_from_name"
                                        placeholder="User Name"
                                        fullWidth
                                        value={formik.values.mail_from_name}
                                        onChange={formik.handleChange}
                                        onKeyPress={(e) => {
                                            if (
                                                e.target.value.length >= 35
                                            ) {
                                                e.preventDefault();
                                            }
                                        }}
                                        error={
                                            formik.touched.mail_from_name &&
                                            Boolean(
                                                formik.errors.mail_from_name
                                            )
                                        }
                                        helperText={
                                            formik.touched.mail_from_name &&
                                            formik.errors.mail_from_name
                                        }
                                    />
                                </Grid>
                            </Grid>

                            {/* 8 */}
                            <Grid item xs={12} alignItems="center" md={6} p={2}>
                                <Grid
                                    item
                                    xs={12}
                                    display="flex"
                                    alignItems="center"
                                >
                                    <CustomFormLabel
                                        htmlFor="google_analytics_key"
                                        sx={{ mt: 0 }}
                                    >
                                        Google Location API Key
                                    </CustomFormLabel>
                                    <Typography
                                    variant="h5"
                                    sx={{
                                        color: (theme) =>
                                            theme.palette.error.main,
                                        marginLeft: "3px",
                                    }}
                                >
                                    {" "}
                                    *
                                </Typography>
                                </Grid>
                                <Grid item xs={12} mt={1}>
                                    <CustomTextField
                                        id="google_analytics_keye"
                                        name="google_analytics_key"
                                        placeholder="Google Location API Key"
                                        fullWidth
                                        value={
                                            formik.values.google_analytics_key
                                        }
                                        onChange={formik.handleChange}
                                        error={
                                            formik.touched
                                                .google_analytics_key &&
                                            Boolean(
                                                formik.errors
                                                    .google_analytics_key
                                            )
                                        }
                                        helperText={
                                            formik.touched
                                                .google_analytics_key &&
                                            formik.errors.google_analytics_key
                                        }
                                    />
                                </Grid>
                            </Grid>

                            {/* Submit Button */}
                            <Grid item xs={12} mt={3}
                            display={'flex'} alignItems={'center'} justifyContent={'center'}
                            >
                                    {/* <Button
                                        color="warning"
                                        variant="contained"
                                        style={{ marginRight: "10px" }}
                                        onClick={() => handleCancel()}
                                    >
                                        Back
                                    </Button> */}
                                <Button
                                    variant="contained"
                                    color="primary"
                                    type="submit"
                                >
                                    Update
                                </Button>
                            </Grid>
                        </Grid>
                    </form>
                </ParentCard>
            </PageContainer>
        </>
    );
};

export default SystemSettings;
