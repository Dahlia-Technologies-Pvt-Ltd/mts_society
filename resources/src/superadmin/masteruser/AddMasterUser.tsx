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
    IconButton,
    AlertTitle,CircularProgress,Checkbox,Autocomplete,MenuItem
} from "@mui/material";
import { IconMinus, IconPlus, IconTrash } from '@tabler/icons';
import { Portal } from '@mui/base';
import Snackbar from "@mui/material/Snackbar";
import { useParams, Link, useNavigate } from "react-router-dom";
import PageContainer from "@src/components/container/PageContainer";
import Breadcrumb from "@src/layouts/full/shared/breadcrumb/Breadcrumb";
import CustomTextField from "@src/components/forms/theme-elements/CustomTextField";
import axios from "axios";
import * as yup from "yup";
import { useFormik } from "formik";
import CustomFormLabel from "@src/components/forms/theme-elements/CustomFormLabel";
import ParentCard from "@src/components/shared/ParentCard";
import "react-quill/dist/quill.snow.css";
import ReactQuill from "react-quill";

const AddSubscription = () => {
    const [isLoading, setIsLoading] = useState(false);
    const [isErrorVisible, setIsErrorVisible] = useState(false);
    const [isSuccessVisible, setIsSuccessVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");
    const [successMessage, setSuccessMessage] = useState("");
    const { id } = useParams(); // Access the 'id' parameter from the URL if it exists
    const navigate = useNavigate();
    const [quillText, setQuillText] = useState("");
    const [selectedCountry, setSelectedCountry] = useState(null);
    const [selectedState, setSelectedState] = useState(null);
    const [countryOptions, setCountryData] = useState([]);
    const [stateOptions, setStateData] = useState([]);
    const appUrl = import.meta.env.VITE_API_URL;

    const BCrumb = [
        {
            to: "/super-admin/society-admin-list",
            title: "Society Admin Management",
        },
        {
            title: id ? "Edit Society Admin" : "Add Society Admin",
        },
    ];
    const handleCountryChange = (event, newValue) => {
        //console.log('county_id', newValue);
        setSelectedCountry(newValue);
        if(newValue){
          fetchState(newValue.id);
          formik.setFieldValue('country_id', newValue.id);
        }else{
          setStateData([]);
          formik.setFieldValue('state_id', '');
          setSelectedState(null);
        }
    };
    const handleStateChange = (event, newValue) => {
        setSelectedState(newValue);
        formik.setFieldValue('state_id', newValue.id);
    };

    // Function to fetch data from the API
  const fetchCountry = async () => {
    try {
        const formData = new FormData();
        formData.append("sortBy", 'name');
        formData.append('sortOrder', 'asc');
        const API_URL = appUrl + '/api/list-country';
        const response = await axios.post(API_URL, formData);
        //console.log(JSON.stringify(response.data.data.data));
        if (response && response.data && response.data.data) {
          setCountryData(response.data.data.data);
        }
    } catch (error) {
        console.error("Error fetching data:", error); // Log any errors
    }
  };

  // Function to fetch data from the API
  const fetchState = async (country_id) => {
    try {
        const formData = new FormData();
        formData.append("sortBy", 'id');
        formData.append('sortOrder', 'asc');
        formData.append('country_id', country_id);
        const API_URL = appUrl + '/api/list-state';
        const response = await axios.post(API_URL, formData);
        //console.log(JSON.stringify(response.data.data.data));
        if (response && response.data && response.data.data) {
          setStateData(response.data.data.data);
        }else{
          setStateData([]);
          formik.setFieldValue('state', '');
        }
    } catch (error) {
        console.error("Error fetching data:", error); // Log any errors
    }
    };

    const validationSchema = yup.object().shape({
        name: yup.string().required("Name is required"),
        email: yup.string().email('Please provide valid email').required("Email is required"),
        phone_number: yup.string().required("Phone Number is required"),
        country_id: yup.string().required("Country is required"),
        state_id: yup.string().required("State is required"),
        city: yup.string().required("City is required"),
    });

    const formik = useFormik({
        initialValues: {
            name: "",
            email: "",
            phone_number:'',
            country_id: '',
            state_id: '',
            city: '',
            pincode: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                setIsLoading(true);
                // Create a FormData object
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append("name", values.name);
                formData.append("email", values.email);
                formData.append("phone_number", values.phone_number);
                formData.append("country_id", values.country_id);
                formData.append("state_id", values.state_id);
                formData.append("city", values.city);
                formData.append("zipcode", values.pincode);
                formData.append("usertype", '1');
                formData.append("username", values.name);
                formData.append("password", values.email);

                // Determine the API URL based on whether it's an edit or add operation
                let API_URL = appUrl + "/api/add-master-user";
                (id) ? formData.append("id", id) : '';
                // Make the API POST request
                const token = localStorage.getItem("authToken");
                const response = await axios.post(API_URL, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "multipart/form-data",
                    },
                });
                setIsSuccessVisible(true);
                setSuccessMessage(response.data.message);
                sessionStorage.setItem("successMessage", response.data.message);
                navigate("/super-admin/society-admin-list");
            } catch (error) {
                // Handle errors here
                setIsErrorVisible(true);
                const validationErrors = error.response.data.validation_error;
                const errorMessages = [];
                // Iterate through each field in the validation_errors object
                for (const field in validationErrors) {
                    if (validationErrors.hasOwnProperty(field)) {
                        const errorMessage = validationErrors[field][0];
                        errorMessages.push(errorMessage);
                    }
                }
                const concatenatedErrorMessage = errorMessages.join("\n");
                setErrorMessage(concatenatedErrorMessage);
                setTimeout(() => {
                    setIsErrorVisible(false);
                }, 5000);
                console.error("Edit spare part error:", error);
            }finally{
                setIsLoading(false);
            }
        },
    });

    // Fetch existing data if it's an edit operation
    useEffect(() => {
        if (id) {
            fetchData();
        }
        fetchCountry();
    }, [id]);

    const fetchData = async () => {
        try {
            const API_URL = `${appUrl}/api/show-master-user/${id}`;
            const token = localStorage.getItem("authToken");

            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                },
            });

            const data = response.data.data;
            formik.setValues({
                name: data.name,
                email: data.email,
                phone_number: data.phone_number,
                country_id: data.country_id,
                state_id: data.state_id,
                city: data.city,
                pincode: data.zipcode,
            });
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    return (
        <>
            <PageContainer
                title="Society Admin"
                description="This is Society Admin"
            >
            <Breadcrumb title="" items={BCrumb} />
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
            <ParentCard
                title={
                    id
                        ? "Edit Society Admin"
                        : "Add Society Admin"
                }
            >
                <form onSubmit={formik.handleSubmit}>
                    <Grid container spacing={2}>
                        {/* 1 */}
                        <Grid item xs={7}>
                            <CustomFormLabel
                                htmlFor="name"
                                sx={{ mt: 0 }}
                            >
                               Name <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="name"
                                name="name"
                                placeholder="Name"
                                fullWidth
                                value={formik.values.name}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.name &&
                                    Boolean(formik.errors.name)
                                }
                                helperText={
                                    formik.touched.name &&
                                    formik.errors.name
                                }
                            />
                        </Grid>        
                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="email"
                                sx={{ mt: 0 }}
                            >
                                Email <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="email"
                                name="email"
                                placeholder="Email"
                                fullWidth
                                value={formik.values.email}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.email &&
                                    Boolean(formik.errors.email)
                                }
                                helperText={
                                    formik.touched.email &&
                                    formik.errors.email
                                }
                            />
                        </Grid>
                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="phone_number"
                                sx={{ mt: 0 }}
                            >
                                Phone Number <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="phone_number"
                                name="phone_number"
                                placeholder="Phone Number"
                                fullWidth
                                value={formik.values.phone_number}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.phone_number &&
                                    Boolean(formik.errors.phone_number)
                                }
                                helperText={
                                    formik.touched.phone_number &&
                                    formik.errors.phone_number
                                }
                                onKeyDown={(e) => {
                                if (!(e.key === 'e' || e.key === '-' || e.key === '+' || !isNaN(Number(e.key)) || e.key === 'Backspace')) {
                                    e.preventDefault();
                                }
                                }}  
                            />
                        </Grid>
                        <Grid item xs={6}>
                            <CustomFormLabel htmlFor="country_id">Country<span style={{color:"red"}}>*</span></CustomFormLabel>
                            <Autocomplete
                            id="country_id"
                            fullWidth
                            options={countryOptions}
                            getOptionLabel={(option) => option.name}
                            isOptionEqualToValue={(option, value) => option.id === value.id}
                            value={selectedCountry}
                            onChange={handleCountryChange}
                            renderOption={(props, option) => (
                                <MenuItem component="li" {...props}>
                                {option.name}
                                </MenuItem>
                            )}
                            renderInput={(params) => (
                                <CustomTextField
                                {...params}
                                placeholder="Select"
                                aria-label="Country"
                                autoComplete="off"
                                inputProps={{
                                    ...params.inputProps,
                                    autoComplete: 'new-password', // disable autocomplete and autofill
                                }}
                                // Add error and helperText props based on Formik validation
                                error={formik.touched.country_id && Boolean(formik.errors.country_id)}
                                helperText={formik.touched.country_id && formik.errors.country_id}
                                />
                            )}
                            />
                        </Grid>

                        <Grid item xs={6}>
                            <CustomFormLabel htmlFor="state_id">State<span style={{color:"red"}}>*</span></CustomFormLabel>
                            <Autocomplete
                            id="state_id"
                            fullWidth
                            options={stateOptions}
                            getOptionLabel={(option) => option.state_name}
                            isOptionEqualToValue={(option, value) => option.id === value.id}
                            value={selectedState}
                            onChange={handleStateChange}
                            renderOption={(props, option) => (
                                <MenuItem component="li" {...props}>
                                {option.state_name}
                                </MenuItem>
                            )}
                            renderInput={(params) => (
                                <CustomTextField
                                {...params}
                                placeholder="Select"
                                aria-label="State"
                                autoComplete="off"
                                inputProps={{
                                    ...params.inputProps,
                                    autoComplete: 'new-password', // disable autocomplete and autofill
                                }}
                                // Add error and helperText props based on Formik validation
                                error={formik.touched.state_id && Boolean(formik.errors.state_id)}
                                helperText={formik.touched.state_id && formik.errors.state_id}
                                />
                            )}
                            />
                        </Grid>
                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="city"
                                sx={{ mt: 0 }}
                            >
                                City <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="city"
                                name="city"
                                placeholder="City"
                                fullWidth
                                value={formik.values.city}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.city &&
                                    Boolean(formik.errors.city)
                                }
                                helperText={
                                    formik.touched.city &&
                                    formik.errors.city
                                }
                            />
                        </Grid>

                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="pincode"
                                sx={{ mt: 0 }}
                            >
                                Pincode
                            </CustomFormLabel>
                            <CustomTextField
                                id="pincode"
                                name="pincode"
                                placeholder="Pincode"
                                fullWidth
                                value={formik.values.pincode}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.pincode &&
                                    Boolean(formik.errors.pincode)
                                }
                                helperText={
                                    formik.touched.pincode &&
                                    formik.errors.pincode
                                }
                                onKeyDown={(e) => {
                                if (!(e.key === 'e' || e.key === '-' || e.key === '+' || !isNaN(Number(e.key)) || e.key === 'Backspace')) {
                                    e.preventDefault();
                                }
                                }}  
                            />
                        </Grid>

                        {/* Submit Button */}
                        <Grid item xs={12} mt={5} display={'flex'} alignItems={'center'} justifyContent={'center'}>
                            <Link to="/super-admin/society-admin-list">
                                <Button
                                    color="warning"
                                    variant="contained"
                                    style={{ marginRight: "10px" }}
                                >
                                    Back
                                </Button>
                            </Link>
                            <Button
                                variant="contained"
                                color="primary"
                                type="submit"
                                disabled={isLoading}
                            >
                                {id ? "Update" : "Submit"}
                                {isLoading && <CircularProgress size={24} color="inherit" />}
                            </Button>
                        </Grid>
                    </Grid>
                </form>
                </ParentCard>
            </PageContainer>
        </>
    );
};

export default AddSubscription;
