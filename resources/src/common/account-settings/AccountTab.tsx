import React, { useState, useEffect } from "react";
import {
    CardContent,
    Grid,
    Typography,
    MenuItem,
    Snackbar,
    Alert,
    Box,
    Avatar,
    Button,
    Stack,
} from "@mui/material";

// components
import BlankCard from "@src/components/shared/BlankCard";
import { Portal } from "@mui/base";
import DriveFolderUploadOutlinedIcon from "@mui/icons-material/DriveFolderUploadOutlined";
import { useParams, Link, useNavigate } from "react-router-dom";
import PageContainer from "@src/components/container/PageContainer";
import Breadcrumb from "@src/layouts/full/shared/breadcrumb/Breadcrumb";
import CustomTextField from "@src/components/forms/theme-elements/CustomTextField";
import CustomSelect from "@src/components/forms/theme-elements/CustomSelect";
import axios from "axios";
import * as yup from "yup";
import { useFormik } from "formik";
import "react-quill/dist/quill.snow.css";
import { useTheme } from "@mui/material/styles";
import ReactQuill from "react-quill";
import CustomFormLabel from "@src/components/forms/theme-elements/CustomFormLabel";
import ParentCard from "@src/components/shared/ParentCard";
import ArticleOutlinedIcon from "@mui/icons-material/ArticleOutlined";
import { addProfilePicture } from '@src/store/apps/eCommerce/ECommerceSlice';
import { useDispatch } from '@src/store/Store';

// images
import user1 from "@src/assets/images/profile/user-1.jpg";

interface locationType {
    value: string;
    label: string;
}

const AccountTab = () => {
    const userType = localStorage.getItem('userType');
    const [location, setLocation] = React.useState("india");
    const dispatch = useDispatch();
    const [open, setOpen] = useState(false);
    const [file, setFile] = useState(null);
    const [oldFile, setOldFile] = useState(null);
    const [selectedFileName, setSelectedFileName] = useState("");
    const setFileValue = (files) => {
        setFile(files[0]);
    };

    const handleFileChange = (event) => {
        const file = event.target.files[0];
        setFile(file);
        setOldFile(file);
    };

    const [url, setUrl] = useState("");

    useEffect(() => {
        if (file) {
            const fileUrl = URL.createObjectURL(file);
            setUrl(fileUrl);
            setOldFile(fileUrl);
        }
    }, [file]);

    const handleChange1 = (event: React.ChangeEvent<HTMLInputElement>) => {
        setLocation(event.target.value);
    };

    const [isErrorVisible, setIsErrorVisible] = useState(false);
    const [isSuccessVisible, setIsSuccessVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");
    const [successMessage, setSuccessMessage] = useState("");
    const [data, setData] = React.useState([]);

    //   currency
    const [currency, setCurrency] = React.useState("india");

    const handleChange2 = (event: React.ChangeEvent<HTMLInputElement>) => {
        setCurrency(event.target.value);
    };

    const validationSchema = yup.object().shape({
        contact_person_name: yup.string().required("Name is required"),
        contact_person_country_code: yup
            .string()
            .required("Country Code is required"),
        contact_person_number: yup
            .string()
            .required("Phone Number is required"),
        contact_person_work_location: yup.string().required("Work Location is required"),
        contact_person_work_email: yup.string().email('Enter a valid email').required("Work Email is required"),
    });

    const formik = useFormik({
        initialValues: {
            contact_person_name: "",
            contact_person_country_code: "",
            contact_person_number: "",
            contact_person_work_location: "",
            contact_person_work_email: "",
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                // Create a FormData object
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append(
                    "contact_person_name",
                    values.contact_person_name
                );
                formData.append(
                    "contact_person_country_code",
                    values.contact_person_country_code
                );
                formData.append(
                    "contact_person_number",
                    values.contact_person_number
                );
                formData.append(
                    "contact_person_work_location",
                    values.contact_person_work_location
                );
                formData.append(
                    "contact_person_work_email",
                    values.contact_person_work_email
                );
                //formData.append("profile_picture", file);

                // Determine the API URL based on whether it's an edit or add operation
                const appUrl = import.meta.env.VITE_API_URL;
                let API_URL = appUrl + "/api/update-customer-profile";

                // Make the API POST request
                const token = localStorage.getItem("authToken");
                const response = await axios.post(API_URL, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "multipart/form-data",
                    },
                });
                //console.log('API Response:', response.data);
                setIsSuccessVisible(true);
                setSuccessMessage(response.data.message);
                setTimeout(() => {
                    setIsSuccessVisible(false);
                }, 5000);
                //localStorage.setItem("successMessage", response.data.message);
                fetchData();
            } catch (error) {
                // Handle errors here
                setIsErrorVisible(true);
                //setErrorMessage(error.response.data.message);
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
                }

                // Now, you have an array (errorMessages) that contains all error messages.
                // You can concatenate them into a single message, separated by line breaks or commas.
                const concatenatedErrorMessage = errorMessages.join("\n");
                setErrorMessage(concatenatedErrorMessage);
                setTimeout(() => {
                    setIsErrorVisible(false);
                }, 5000);
                console.error("Edit spare part error:", error);
            }
        },
    });

    const handleSubmit = () => {
        const appUrl = import.meta.env.VITE_API_URL;
        const formData = new FormData();
        formData.append("profile_picture", file);

        const API_URL = `${appUrl}/api/update-profile-picture`;
        const token = localStorage.getItem("authToken");
        const userId = localStorage.getItem("userId");
        formData.append("id", userId);
        axios
            .post(API_URL, formData, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                },
            })
            .then((response) => {
                setIsSuccessVisible(true);
                setSuccessMessage(response.data.message);
                setFile(null);
                setOldFile(null);
                setSelectedFileName("");
                setUrl(response.data.data.profile_picture);
                localStorage.setItem('profilePicture', response.data.data.profile_picture);
                dispatch(addProfilePicture(response.data.data.profile_picture));
                fetchData();
                setTimeout(() => {
                    setIsSuccessVisible(false);
                }, 5000);
                //console.log('Edit spare part response:', response.data);
            })
            .catch((error) => {
                setIsErrorVisible(true);
                //setErrorMessage(error.response.data.message);
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
                }

                // Now, you have an array (errorMessages) that contains all error messages.
                // You can concatenate them into a single message, separated by line breaks or commas.
                const concatenatedErrorMessage = errorMessages.join("\n");
                setErrorMessage(concatenatedErrorMessage);
                setTimeout(() => {
                    setIsErrorVisible(false);
                }, 5000);
                console.error("Edit spare part error:", error);
            });
    };

    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = `${appUrl}/api/get-profile`;
            const token = localStorage.getItem("authToken");

            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                },
            });

            const data = response.data.data;
            setUrl(data.profile_picture);
            setData(data);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    return (
        <Grid container spacing={3}>
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

            {/* Change Profile */}
            <Grid item xs={12} lg={6}>
                <BlankCard>
                    <CardContent sx={{ height: "350px" }}>
                        <Typography variant="h5" mb={1}>
                            Change Profile
                        </Typography>
                        <Typography color="textSecondary" mb={3}>
                            Change your profile picture from here
                        </Typography>
                        <Box
                            textAlign="center"
                            display="flex"
                            justifyContent="center"
                        >
                            <Box>
                                <Avatar
                                    src={url ? url : ''}
                                    alt={user1}
                                    sx={{
                                        width: 120,
                                        height: 120,
                                        margin: "0 auto",
                                        border: 1,
                                        borderColor: "black",
                                    }}
                                />
                                <Stack
                                    direction="row"
                                    justifyContent="center"
                                    spacing={2}
                                    my={3}
                                >
                                    { oldFile ?
                                    <Button
                                        variant="contained"
                                        color="primary"
                                        component="label"
                                        onClick={() => handleSubmit()}
                                    >
                                        Update
                                    </Button>
                                    :
                                    <Button variant="contained" color="primary" component="label">
                                        Upload
                                        <input hidden accept="image/*" name="profile_pic" id="profile_pic" type="file" onChange={handleFileChange} />
                                    </Button>
                                    }
                                </Stack>
                                <Typography
                                    variant="subtitle1"
                                    color="textSecondary"
                                    mb={4}
                                >
                                    Allowed JPG or PNG. Max size of 800Kb.
                                </Typography>
                            </Box>
                        </Box>
                    </CardContent>
                </BlankCard>
            </Grid>
              {/*User Details */}
              {(userType == '2' || userType == '1') && <Grid item xs={12} lg={6}>
                <BlankCard>
                    <CardContent sx={{ height: "350px" }}>
                        <Typography variant="h5" mb={2}>
                            User Details
                        </Typography>

                        <Grid container>
                            <Grid item lg={6} xs={12} mt={1}>
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    User Code
                                </Typography>
                                <Typography
                                    variant="subtitle1"
                                    mb={0.5}
                                    fontWeight={600}
                                >
                                    {data.user_code === ""
                                        ? "n/a"
                                        : data.user_code}
                                </Typography>
                            </Grid>
                            <Grid item lg={6} xs={12} mt={1}>
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    Name
                                </Typography>
                                <Typography
                                    variant="subtitle1"
                                    mb={0.5}
                                    fontWeight={600}
                                >
                                    {data.name === "" ? "-" : data.name}
                                </Typography>
                            </Grid>
                            <Grid item lg={6} xs={12} mt={1}>
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    Email ID
                                </Typography>
                                <Typography
                                    variant="subtitle1"
                                    fontWeight={600}
                                    mb={0.5}
                                >
                                    {data.email_id === ""
                                        ? "-"
                                        : data.email_id}
                                </Typography>
                            </Grid>
                            <Grid item lg={6} xs={12} mt={1}>
                                <Typography
                                    variant="body2"
                                    color="text.secondary"
                                >
                                    Phone Number
                                </Typography>
                                <Typography
                                    variant="subtitle1"
                                    fontWeight={600}
                                    mb={0.5}
                                >
                                    {data.phone_number === ""
                                        ? "-"
                                        : data.phone_number}
                                </Typography>
                            </Grid>
                       
                        </Grid>
                    </CardContent>
                </BlankCard>
            </Grid>}
        </Grid>
    );
};

export default AccountTab;