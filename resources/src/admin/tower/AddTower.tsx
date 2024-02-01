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
    AlertTitle,CircularProgress,Checkbox
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
import AddIcon from '@mui/icons-material/Add';
import RemoveIcon from '@mui/icons-material/Remove';

const AddTower = () => {
    const [isLoading, setIsLoading] = useState(false);
    const [isErrorVisible, setIsErrorVisible] = useState(false);
    const [isSuccessVisible, setIsSuccessVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");
    const [successMessage, setSuccessMessage] = useState("");
    const { id } = useParams(); // Access the 'id' parameter from the URL if it exists
    const navigate = useNavigate();
    const [quillText, setQuillText] = useState("");

    const BCrumb = [
        {
            to: "/admin/tower-list",
            title: "Tower List",
        },
        {
            title: id ? "Edit Tower" : "Add Tower",
        },
    ];

    const validationSchema = yup.object().shape({
        tower_name: yup.string().required("Tower Name is required"),
    });

    const formik = useFormik({
        initialValues: {
            tower_name: "",
            wing_name: [""],
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                setIsLoading(true);
                // Create a FormData object
                console.log('wing data', values.wing_name);
                return;
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append("tower_name", values.tower_name);
                // Determine the API URL based on whether it's an edit or add operation
                const appUrl = import.meta.env.VITE_API_URL;
                let API_URL = appUrl + "/api/add-tower";
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
                navigate("/admin/tower-list");
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
    
    // Helper function to add a new wing name field
    const addWingName = () => {
        formik.setFieldValue('wing_name', [
        ...formik.values.wing_name,
        ''
        ]);
    };
    // Helper function to remove a wing name field
    const removeWingName = (index) => {
        const updatedWingNames = [...formik.values.wing_name];
        updatedWingNames.splice(index, 1);
        formik.setFieldValue('wing_name', updatedWingNames);
    };

    // Fetch existing data if it's an edit operation
    useEffect(() => {
        if (id) {
            fetchData();
        }
    }, [id]);

    const fetchData = async () => {
        try {
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = `${appUrl}/api/show-tower/${id}`;
            const token = localStorage.getItem("authToken");

            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                },
            });

            const data = response.data.data;
            formik.setValues({
                tower_name: data.tower_name,
                wing_name: data.wing_name,
            });
            setQuillText(data.features);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    return (
        <>
            <PageContainer
                title="Tower"
                description="This is Tower"
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
                        ? "Edit Tower"
                        : "Add Tower"
                }
            >
                <form onSubmit={formik.handleSubmit}>
                    <Grid container spacing={2}>
                        {/* 1 */}
                        <Grid item xs={7}>
                            <CustomFormLabel
                                htmlFor="tower_name"
                                sx={{ mt: 0 }}
                            >
                                Tower Name <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="tower_name"
                                name="tower_name"
                                placeholder="Tower Name"
                                fullWidth
                                value={formik.values.tower_name}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.tower_name &&
                                    Boolean(formik.errors.tower_name)
                                }
                                helperText={
                                    formik.touched.tower_name &&
                                    formik.errors.tower_name
                                }
                            />
                        </Grid>        
                       
                        <Grid item xs={6}>
                            <CustomFormLabel htmlFor={`wing_name`} sx={{ mt: 0 }}>
                                Wing Name
                            </CustomFormLabel>
                            {formik.values.wing_name.map((wingName, index) => (
                                <Grid container spacing={1} alignItems="center" key={index}>
                                    <Grid item xs={8} mt={(index === 0) ? 0 : 1}>
                                        <CustomTextField
                                            id={`wing_name_${index}`}
                                            name={`wing_name[${index}]`}
                                            placeholder="Wing Name"
                                            fullWidth
                                            value={wingName}
                                            onChange={formik.handleChange}
                                            error={
                                            formik.touched.wing_name &&
                                            formik.touched.wing_name[index] &&
                                            Boolean(formik.errors.wing_name) &&
                                            Boolean(formik.errors.wing_name[index])
                                            }
                                            helperText={
                                            formik.touched.wing_name &&
                                            formik.touched.wing_name[index] &&
                                            formik.errors.wing_name &&
                                            formik.errors.wing_name[index]
                                            }
                                        />
                                    </Grid>
                                    <Grid item xs={2} mt={(index === 0) ? 0 : 1}>
                                    {(index === 0) ? 
                                        <Button
                                            variant="outlined"
                                            color="success"
                                            onClick={addWingName}
                                            title="Add"
                                        >
                                            <AddIcon />
                                        </Button>
                                    :
                                        <Button
                                            variant="outlined"
                                            color="error"
                                            onClick={() => removeWingName(index)}
                                            title="Remove"
                                        >
                                            <RemoveIcon />
                                        </Button>
                                    }
                                    </Grid>
                                </Grid>
                            ))}
                        </Grid>
                        
                        {/* Submit Button */}
                        <Grid item xs={12} mt={5} display={'flex'} alignItems={'center'} justifyContent={'center'}>
                            <Link to="/admin/tower-list">
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

export default AddTower;
