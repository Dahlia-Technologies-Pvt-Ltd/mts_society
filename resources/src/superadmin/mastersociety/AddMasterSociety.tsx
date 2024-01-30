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

const AddMasterSociety = () => {
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
    const [selectedAdmin, setSelectedAdmin] = useState(null);
    const [selectedSubscription, setSelectedSubscription] = useState(null);
    const [countryOptions, setCountryData] = useState([]);
    const [stateOptions, setStateData] = useState([]);
    const [adminOptions, setAdminData] = useState([]);
    const [subscriptionOption, setMasterSubscriptionData] = useState([]);
    const appUrl = import.meta.env.VITE_API_URL;

    const BCrumb = [
        {
            to: "/super-admin/society-list",
            title: "Society Management",
        },
        {
            title: id ? "Edit Society" : "Add Society",
        },
    ];
    const handleAdminChange = (event, newValue) => {
        setSelectedAdmin(newValue);
        if(newValue){
          formik.setFieldValue('user_id', newValue.id);
          formik.setFieldValue('user_email', newValue.email);
          formik.setFieldValue('user_phone', newValue.phone_number);
        }else{
          formik.setFieldValue('user_id', '');
        }
    };
    const handleSubscriptionChange = (event, newValue) => {
        setSelectedSubscription(newValue);
        if(newValue){
          formik.setFieldValue('subscription_id', newValue.id);
        }else{
          formik.setFieldValue('subscription_id', '');
        }
    };
    const handleCountryChange = (event, newValue) => {
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
    const fetchAdmin = async () => {
        try {
            const formData = new FormData();
            formData.append("sortBy", 'id');
            formData.append('sortOrder', 'asc');
            formData.append('usertype', '1');
            const API_URL = appUrl + '/api/list-master-user';
            const response = await axios.post(API_URL, formData);
            //console.log(JSON.stringify(response.data.data.data));
            if (response && response.data && response.data.data) {
            setAdminData(response.data.data.data);
            }else{
            setAdminData([]);
            }
        } catch (error) {
            console.error("Error fetching data:", error); // Log any errors
        }
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
        society_name: yup.string().required("Society Name is required"),
        address: yup.string().required("Address is required"),
        email: yup.string().email('Please provide valid email').required("Email is required"),
        phone_number: yup.string().required("Phone Number is required"),
        country_id: yup.string().required("Country is required"),
        state_id: yup.string().required("State is required"),
        city: yup.string().required("City is required"),
        user_id: yup.string().required("Society Admin is required"),
    });

    const formik = useFormik({
        initialValues: {
            society_name: "",
            address: "",
            email: "",
            phone_number:'',
            country_id: '',
            state_id: '',
            city: '',
            zipcode: '',
            user_id: '',
            user_phone: '',
            user_email: '',
            subscription_id: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                setIsLoading(true);
                // Create a FormData object
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append("society_name", values.society_name);
                formData.append("email", values.email);
                formData.append("phone_number", values.phone_number);
                formData.append("country_id", values.country_id);
                formData.append("state_id", values.state_id);
                formData.append("city", values.city);
                formData.append("zipcode", values.zipcode);
                formData.append("address", values.address);
                formData.append("user_id", values.user_id);
                formData.append("subscription_id", values.subscription_id);

                // Determine the API URL based on whether it's an edit or add operation
                let API_URL = appUrl + "/api/add-society";
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
                navigate("/super-admin/society-list");
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
        fetchAdmin();
        fetchMasterSubscription();
    }, [id]);

    useEffect(() => {
        // Find the country from the fetched list based on country_id
        const selectedCountryData = countryOptions.find(
          (country) => country.id === parseInt(formik.values.country_id)
        );
        // If the country is found, set it as the selectedCountry
        if (selectedCountryData) {
          setSelectedCountry(selectedCountryData);
        }
    }, [countryOptions, formik.values.country_id]);

    // Effect to fetch state when the country changes
useEffect(() => {
    const fetchData = async () => {
        try {
            if (selectedCountry) {
                const formData = new FormData();
                formData.append("sortBy", 'id');
                formData.append('sortOrder', 'asc');
                formData.append('country_id', selectedCountry.id);
                const API_URL = appUrl + '/api/list-state';
                const response = await axios.post(API_URL, formData);
                
                // Update state options with the fetched data
                setStateData(response.data.data.data);

                // Find the state from the fetched list based on state_id
                const selectedStateData = response.data.data.data.find(
                    (state) => state.id === parseInt(formik.values.state_id)
                );

                // If the state is found, set it as the selectedState
                if (selectedStateData) {
                    setSelectedState(selectedStateData);
                }
            }
        } catch (error) {
            console.error("Error fetching data:", error);
            setStateData([]); // Reset state data in case of an error
        }
    };

    fetchData();
}, [selectedCountry, formik.values.state_id]);


    useEffect(() => {
        // Find the admin from the fetched list based on user_id
        const selectedAdminData = adminOptions.find(
            (admin) => admin.id === parseInt(formik.values.user_id)
        );
        // If the admin is found, set it as the selectedAdmin
        if (selectedAdminData) {
            setSelectedAdmin(selectedAdminData);
        }
    }, [adminOptions, formik.values.user_id]);

     // Function to fetch data from the API
  const fetchMasterSubscription = async () => {
        try {
            const formData = new FormData();
            formData.append("sortBy", 'id');
            formData.append('sortOrder', 'asc');
            const API_URL = appUrl + '/api/list-master-subscription';
            const response = await axios.post(API_URL, formData);
            //console.log(JSON.stringify(response.data.data.data));
            if (response && response.data && response.data.data) {
            setMasterSubscriptionData(response.data.data.data);
            }else{
            setMasterSubscriptionData([]);
            }
        } catch (error) {
            console.error("Error fetching data:", error); // Log any errors
        }
    };
    const fetchData = async () => {
        try {
            const API_URL = `${appUrl}/api/show-society/${id}`;
            const token = localStorage.getItem("authToken");

            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                },
            });

            const data = response.data.data;
            formik.setValues({
                society_name: data.society_name,
                email: data.email,
                phone_number: data.phone_number,
                country_id: data.country_id,
                state_id: data.state_id,
                city: data.city,
                zipcode: data.zipcode,
                address: data.address,
                user_id: data.society_owner.id,
                user_phone: data.society_owner.phone_number,
                user_email: data.society_owner.email,
                subscription_id: '',
            });
            fetchState(data.country_id);
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
                        ? "Edit Society"
                        : "Add Society"
                }
            >
                <form onSubmit={formik.handleSubmit}>
                    <Grid container spacing={2}>
                        {/* 1 */}
                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="society_name"
                                sx={{ mt: 0 }}
                            >
                               Society Name <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="society_name"
                                name="society_name"
                                placeholder="Society Name"
                                fullWidth
                                value={formik.values.society_name}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.society_name &&
                                    Boolean(formik.errors.society_name)
                                }
                                helperText={
                                    formik.touched.society_name &&
                                    formik.errors.society_name
                                }
                            />
                        </Grid>        
                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="address"
                                sx={{ mt: 0 }}
                            >
                               Address <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="address"
                                name="address"
                                placeholder="Address"
                                fullWidth
                                value={formik.values.address}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.address &&
                                    Boolean(formik.errors.address)
                                }
                                helperText={
                                    formik.touched.address &&
                                    formik.errors.address
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
                                htmlFor="zipcode"
                                sx={{ mt: 0 }}
                            >
                                Zipcode
                            </CustomFormLabel>
                            <CustomTextField
                                id="zipcode"
                                name="zipcode"
                                placeholder="Zipcode"
                                fullWidth
                                value={formik.values.zipcode}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.zipcode &&
                                    Boolean(formik.errors.zipcode)
                                }
                                helperText={
                                    formik.touched.zipcode &&
                                    formik.errors.zipcode
                                }
                                onKeyDown={(e) => {
                                if (!(e.key === 'e' || e.key === '-' || e.key === '+' || !isNaN(Number(e.key)) || e.key === 'Backspace')) {
                                    e.preventDefault();
                                }
                                }}  
                            />
                        </Grid>
                        <Grid item xs={4}>
                            <CustomFormLabel htmlFor="user_id">Society Admin<span style={{color:"red"}}>*</span></CustomFormLabel>
                            <Autocomplete
                            id="user_id"
                            fullWidth
                            options={adminOptions}
                            getOptionLabel={(option) => option.name}
                            isOptionEqualToValue={(option, value) => option.id === value.id}
                            value={selectedAdmin}
                            onChange={handleAdminChange}
                            renderOption={(props, option) => (
                                <MenuItem component="li" {...props}>
                                {option.name}
                                </MenuItem>
                            )}
                            renderInput={(params) => (
                                <CustomTextField
                                {...params}
                                placeholder="Society Admin"
                                aria-label="Admin"
                                autoComplete="off"
                                inputProps={{
                                    ...params.inputProps,
                                    autoComplete: 'new-password', // disable autocomplete and autofill
                                }}
                                // Add error and helperText props based on Formik validation
                                error={formik.touched.user_id && Boolean(formik.errors.user_id)}
                                helperText={formik.touched.user_id && formik.errors.user_id}
                                />
                            )}
                            />
                        </Grid>
                        <Grid item xs={4}>
                            <CustomFormLabel
                                htmlFor="user_phone"
                                sx={{ mt: 4 }}
                            >
                                Phone Number
                            </CustomFormLabel>
                            <CustomTextField
                                id="user_phone"
                                name="user_phone"
                                placeholder="Phone Number"
                                fullWidth
                                value={formik.values.user_phone}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.user_phone &&
                                    Boolean(formik.errors.user_phone)
                                }
                                helperText={
                                    formik.touched.user_phone &&
                                    formik.errors.user_phone
                                }
                                disabled={true}
                            />
                        </Grid>
                        <Grid item xs={4}>
                            <CustomFormLabel
                                htmlFor="user_email"
                                sx={{ mt: 4 }}
                            >
                                Email ID
                            </CustomFormLabel>
                            <CustomTextField
                                id="user_email"
                                name="user_email"
                                placeholder="Email ID"
                                fullWidth
                                value={formik.values.user_email}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.user_email &&
                                    Boolean(formik.errors.user_email)
                                }
                                helperText={
                                    formik.touched.user_email &&
                                    formik.errors.user_email
                                }
                                disabled={true}
                                readonly
                            />
                        </Grid>
                        <Grid item xs={4}>
                            <CustomFormLabel htmlFor="subscription_id">Subscription Plan<span style={{color:"red"}}>*</span></CustomFormLabel>
                            <Autocomplete
                            id="subscription_id"
                            fullWidth
                            options={subscriptionOption}
                            getOptionLabel={(option) => option.subscription_plan}
                            isOptionEqualToValue={(option, value) => option.id === value.id}
                            value={selectedSubscription}
                            onChange={handleSubscriptionChange}
                            renderOption={(props, option) => (
                                <MenuItem component="li" {...props}>
                                {option.subscription_plan}
                                </MenuItem>
                            )}
                            renderInput={(params) => (
                                <CustomTextField
                                {...params}
                                placeholder="Subscription Plan"
                                aria-label="Admin"
                                autoComplete="off"
                                inputProps={{
                                    ...params.inputProps,
                                    autoComplete: 'new-password', // disable autocomplete and autofill
                                }}
                                // Add error and helperText props based on Formik validation
                                error={formik.touched.subscription_id && Boolean(formik.errors.subscription_id)}
                                helperText={formik.touched.subscription_id && formik.errors.subscription_id}
                                />
                            )}
                            />
                        </Grid>
                        {/* Submit Button */}
                        <Grid item xs={12} mt={5} display={'flex'} alignItems={'center'} justifyContent={'center'}>
                            <Link to="/super-admin/society-list">
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

export default AddMasterSociety;
